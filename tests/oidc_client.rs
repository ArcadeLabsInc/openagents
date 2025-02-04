use axum::{
    body::{to_bytes, Body},
    http::{Request, StatusCode},
    Router,
};
use base64::engine::general_purpose::URL_SAFE_NO_PAD;
use base64::Engine;
use serde_json::json;
use tower::ServiceExt;
use tracing::{debug, info};
use uuid::Uuid;
use wiremock::{
    matchers::{method, path},
    Mock, MockServer, ResponseTemplate,
};

mod common;
use common::setup_test_db;
use openagents::server::config::AppConfig;

const MAX_SIZE: usize = 1024 * 1024; // 1MB limit for response bodies

fn init_logging() {
    let _ = tracing_subscriber::fmt()
        .with_env_filter(tracing_subscriber::EnvFilter::from_default_env())
        .with_file(true)
        .with_line_number(true)
        .with_thread_ids(true)
        .with_thread_names(true)
        .with_target(true)
        .try_init();
}

struct TestContext {
    mock_server: MockServer,
    app: Router,
    user_id: String,
}

impl TestContext {
    async fn new() -> Self {
        let mock_server = MockServer::start().await;
        info!("Mock server started at: {}", mock_server.uri());

        // Set up test database
        let _pool = setup_test_db().await;

        // Use DATABASE_URL from environment, fall back to CI default
        let database_url = std::env::var("DATABASE_URL")
            .unwrap_or_else(|_| "postgres://postgres:password@localhost:5432/postgres".to_string());

        // Create config for this test's mock server
        let config = AppConfig {
            oidc_auth_url: format!("{}/auth", mock_server.uri()),
            oidc_token_url: format!("{}/token", mock_server.uri()),
            oidc_client_id: "test_client".to_string(),
            oidc_client_secret: "test_secret".to_string(),
            oidc_redirect_uri: "http://localhost:8000/auth/callback".to_string(),
            database_url,
        };

        let app = openagents::server::config::configure_app_with_config(Some(config));
        info!("App configured");

        let user_id = format!("test_user_{}", Uuid::new_v4());

        Self {
            mock_server,
            app,
            user_id,
        }
    }

    async fn mock_token_success(&self) {
        Mock::given(method("POST"))
            .and(path("/token"))
            .respond_with(ResponseTemplate::new(200).set_body_json(json!({
                "access_token": "test_access_token",
                "id_token": self.create_test_jwt(),
                "token_type": "Bearer",
                "expires_in": 3600
            })))
            .mount(&self.mock_server)
            .await;
        info!("Mock token endpoint configured for success");
    }

    async fn mock_token_error(&self) {
        Mock::given(method("POST"))
            .and(path("/token"))
            .respond_with(ResponseTemplate::new(400).set_body_json(json!({
                "error": "invalid_grant",
                "error_description": "Invalid authorization code"
            })))
            .mount(&self.mock_server)
            .await;
        info!("Mock token endpoint configured for error");
    }

    fn create_test_jwt(&self) -> String {
        let header = URL_SAFE_NO_PAD.encode(r#"{"alg":"HS256","typ":"JWT"}"#);
        let claims = URL_SAFE_NO_PAD.encode(&format!(r#"{{"sub":"{}"}}"#, self.user_id));
        let token = format!("{}.{}.signature", header, claims);
        debug!("Created test JWT: {}", token);
        token
    }
}

#[tokio::test]
async fn test_full_auth_flow() {
    init_logging();

    let ctx = TestContext::new().await;
    ctx.mock_token_success().await;

    // Test login redirect
    info!("Testing login redirect");
    let response = ctx
        .app
        .clone()
        .oneshot(
            Request::builder()
                .uri("/auth/login")
                .body(Body::empty())
                .unwrap(),
        )
        .await
        .unwrap();

    info!("Login redirect response status: {}", response.status());
    assert_eq!(response.status(), StatusCode::TEMPORARY_REDIRECT);

    let location = response
        .headers()
        .get("location")
        .unwrap()
        .to_str()
        .unwrap();
    info!("Login redirect location: {}", location);
    assert!(location.contains("/auth"));
    assert!(location.contains("flow=login"));

    // Test callback
    info!("Testing callback");
    let response = ctx
        .app
        .clone()
        .oneshot(
            Request::builder()
                .uri("/auth/callback?code=test_code&flow=login")
                .body(Body::empty())
                .unwrap(),
        )
        .await
        .unwrap();

    info!("Callback response status: {}", response.status());
    assert_eq!(response.status(), StatusCode::TEMPORARY_REDIRECT);

    let cookie = response
        .headers()
        .get("set-cookie")
        .unwrap()
        .to_str()
        .unwrap();
    info!("Callback set-cookie: {}", cookie);
    assert!(cookie.contains("session="));

    // Test logout
    info!("Testing logout");
    let response = ctx
        .app
        .oneshot(
            Request::builder()
                .uri("/auth/logout")
                .body(Body::empty())
                .unwrap(),
        )
        .await
        .unwrap();

    info!("Logout response status: {}", response.status());
    assert_eq!(response.status(), StatusCode::TEMPORARY_REDIRECT);

    let cookie = response
        .headers()
        .get("set-cookie")
        .unwrap()
        .to_str()
        .unwrap();
    info!("Logout set-cookie: {}", cookie);
    assert!(cookie.contains("session=;"));
}

#[tokio::test]
async fn test_invalid_callback() {
    init_logging();

    let ctx = TestContext::new().await;
    ctx.mock_token_error().await;

    // Test callback with invalid code
    info!("Testing callback with invalid code");
    let response = ctx
        .app
        .oneshot(
            Request::builder()
                .uri("/auth/callback?code=invalid_code&flow=login")
                .body(Body::empty())
                .unwrap(),
        )
        .await
        .unwrap();

    info!("Invalid callback response status: {}", response.status());
    assert_eq!(response.status(), StatusCode::BAD_GATEWAY);

    let body = to_bytes(response.into_body(), MAX_SIZE).await.unwrap();
    let error_response: serde_json::Value = serde_json::from_slice(&body).unwrap();
    info!("Invalid callback error response: {:?}", error_response);

    assert!(error_response["error"]
        .as_str()
        .unwrap()
        .contains("Token exchange failed"));
}

#[tokio::test]
async fn test_duplicate_login() {
    init_logging();

    let ctx = TestContext::new().await;
    ctx.mock_token_success().await;

    // First login should succeed
    info!("Testing first login");
    let response = ctx
        .app
        .clone()
        .oneshot(
            Request::builder()
                .uri("/auth/callback?code=test_code&flow=signup")
                .body(Body::empty())
                .unwrap(),
        )
        .await
        .unwrap();

    info!("First login response status: {}", response.status());
    assert_eq!(response.status(), StatusCode::TEMPORARY_REDIRECT);

    // Second login should also succeed (update last_login_at)
    info!("Testing second login");
    let response = ctx
        .app
        .oneshot(
            Request::builder()
                .uri("/auth/callback?code=test_code&flow=signup")
                .body(Body::empty())
                .unwrap(),
        )
        .await
        .unwrap();

    info!("Second login response status: {}", response.status());
    assert_eq!(response.status(), StatusCode::TEMPORARY_REDIRECT);
}
