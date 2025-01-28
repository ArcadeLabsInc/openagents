use axum::{
    extract::{Query, State},
    http::{header::SET_COOKIE, HeaderMap, StatusCode},
    response::{IntoResponse, Redirect},
    Json,
};
use axum_extra::extract::cookie::{Cookie, SameSite};
use serde::{Deserialize, Serialize};
use sqlx::PgPool;
use time::Duration;
use tracing::{debug, error, info};

use crate::server::services::auth::{OIDCConfig, OIDCService};

const SESSION_COOKIE_NAME: &str = "session";
const SESSION_DURATION_DAYS: i64 = 7;

#[derive(Debug, Deserialize)]
pub struct CallbackParams {
    code: String,
}

#[derive(Debug, Serialize)]
pub struct ErrorResponse {
    error: String,
}

#[derive(Debug, Serialize)]
pub struct LoginResponse {
    url: String,
}

#[derive(Clone)]
pub struct AppState {
    pub service: OIDCService,
}

impl AppState {
    pub fn new(config: OIDCConfig, pool: PgPool) -> Self {
        Self {
            service: OIDCService::new(pool, config),
        }
    }
}

pub async fn login(State(state): State<AppState>) -> impl IntoResponse {
    let auth_url = state.service.authorization_url_for_login().unwrap();
    debug!("Redirecting to auth URL: {}", auth_url);
    Redirect::temporary(&auth_url)
}

pub async fn signup(State(state): State<AppState>) -> impl IntoResponse {
    let auth_url = state.service.authorization_url_for_signup().unwrap();
    debug!("Redirecting to signup URL: {}", auth_url);
    Redirect::temporary(&auth_url)
}

pub async fn callback(
    State(state): State<AppState>,
    Query(params): Query<CallbackParams>,
) -> Result<impl IntoResponse, (StatusCode, Json<ErrorResponse>)> {
    debug!("Received callback with code length: {}", params.code.len());

    // Exchange code for tokens and get/create user
    let user = state
        .service
        .signup(params.code)
        .await
        .map_err(|e| {
            error!("Authentication error: {}", e.to_string());
            (
                StatusCode::from(e.clone()),
                Json(ErrorResponse {
                    error: e.to_string(),
                }),
            )
        })?;

    info!("User authenticated with scramble_id: {}", user.scramble_id);

    // Create session cookie
    let cookie = Cookie::build((SESSION_COOKIE_NAME, user.scramble_id.clone()))
        .path("/")
        .secure(true)
        .http_only(true)
        .same_site(SameSite::Lax)
        .max_age(Duration::days(SESSION_DURATION_DAYS))
        .build();

    debug!("Created session cookie: {}", cookie.to_string());

    // Set cookie and redirect to home
    let mut headers = HeaderMap::new();
    headers.insert(SET_COOKIE, cookie.to_string().parse().unwrap());

    Ok((headers, Redirect::temporary("/")))
}

pub async fn logout() -> impl IntoResponse {
    debug!("Processing logout request");

    // Create cookie that will expire immediately
    let cookie = Cookie::build((SESSION_COOKIE_NAME, ""))
        .path("/")
        .secure(true)
        .http_only(true)
        .same_site(SameSite::Lax)
        .max_age(Duration::seconds(0))
        .build();

    let mut headers = HeaderMap::new();
    headers.insert(SET_COOKIE, cookie.to_string().parse().unwrap());

    debug!("Created logout cookie: {}", cookie.to_string());

    (headers, Redirect::temporary("/"))
}