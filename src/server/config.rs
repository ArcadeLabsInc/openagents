use super::services::{deepseek::DeepSeekService, github_issue::GitHubService, RepomapService};
use super::tools::create_tools;
use super::ws::transport::WebSocketState;
use crate::{routes, server};
use axum::{
    routing::{get, post},
    Router,
};
use std::{env, sync::Arc};
use tower_http::services::ServeDir;
use tracing::info;

#[derive(Clone)]
pub struct AppState {
    pub ws_state: Arc<WebSocketState>,
    pub repomap_service: Arc<RepomapService>,
    pub auth_state: Arc<server::handlers::auth::AuthState>,
}

pub fn configure_app() -> Router {
    // Load environment variables
    dotenvy::dotenv().ok();

    // Create shared services
    let tool_model = Arc::new(DeepSeekService::new(
        env::var("DEEPSEEK_API_KEY").expect("DEEPSEEK_API_KEY must be set"),
    ));

    let chat_model = Arc::new(DeepSeekService::new(
        env::var("DEEPSEEK_API_KEY").expect("DEEPSEEK_API_KEY must be set"),
    ));

    let github_service = Arc::new(
        GitHubService::new(Some(
            env::var("GITHUB_TOKEN").expect("GITHUB_TOKEN must be set"),
        ))
        .expect("Failed to create GitHub service"),
    );

    // Create available tools
    let tools = create_tools();

    // Create WebSocket state with services
    let ws_state = Arc::new(WebSocketState::new(tool_model, chat_model, github_service.clone(), tools));

    // Initialize repomap service
    let aider_api_key = env::var("AIDER_API_KEY").unwrap_or_else(|_| "".to_string());
    let repomap_service = Arc::new(RepomapService::new(aider_api_key));

    // Create auth state with OIDC config
    let oidc_config = server::services::auth::OIDCConfig::new(
        env::var("OIDC_CLIENT_ID").expect("OIDC_CLIENT_ID must be set"),
        env::var("OIDC_CLIENT_SECRET").expect("OIDC_CLIENT_SECRET must be set"),
        env::var("OIDC_REDIRECT_URI")
            .unwrap_or_else(|_| "http://localhost:8000/auth/callback".to_string()),
        env::var("OIDC_AUTH_URL").expect("OIDC_AUTH_URL must be set"),
        env::var("OIDC_TOKEN_URL").expect("OIDC_TOKEN_URL must be set"),
    )
    .expect("Failed to create OIDC config");

    let pool = sqlx::PgPool::connect_lazy(&env::var("DATABASE_URL").expect("DATABASE_URL must be set"))
        .expect("Failed to create database pool");

    let auth_state = Arc::new(server::handlers::auth::AuthState::new(oidc_config, pool));

    // Create shared app state
    let app_state = AppState {
        ws_state,
        repomap_service,
        auth_state,
    };

    // Create the main router
    Router::new()
        // Main routes
        .route("/", get(routes::home))
        .route("/chat", get(routes::chat))
        .route("/ws", get(server::ws::ws_handler))
        .route("/onyx", get(routes::mobile_app))
        .route("/services", get(routes::business))
        .route("/video-series", get(routes::video_series))
        .route("/company", get(routes::company))
        .route("/coming-soon", get(routes::coming_soon))
        .route("/health", get(routes::health_check))
        .route("/repomap", get(routes::repomap))
        .route("/cota", get(routes::cota))
        // Auth pages
        .route("/login", get(routes::login))
        .route("/signup", get(routes::signup))
        // Auth routes
        .route("/auth/login", get(server::handlers::auth::login))
        .route("/auth/signup", post(server::handlers::auth::handle_signup))
        .route("/auth/callback", get(server::handlers::auth::callback))
        .route("/auth/logout", get(server::handlers::auth::logout))
        // Repomap routes
        .route("/repomap/generate", post(routes::generate_repomap))
        // Static files
        .nest_service("/assets", ServeDir::new("./assets").precompressed_gzip())
        .nest_service(
            "/templates",
            ServeDir::new("./templates").precompressed_gzip(),
        )
        // State
        .with_state(app_state)
}