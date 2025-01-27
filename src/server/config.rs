use super::services::{
    deepseek::{DeepSeekService, Tool},
    github_issue::GitHubService,
    RepomapService,
};
use super::ws::transport::WebSocketState;
use axum::{routing::get, Router};
use serde_json::json;
use std::{env, sync::Arc};
use tower_http::services::ServeDir;

use crate::routes;

fn create_tools() -> Vec<Tool> {
    vec![
        // GitHub issue tool
        DeepSeekService::create_tool(
            "read_github_issue".to_string(),
            Some("Read a GitHub issue by number".to_string()),
            json!({
                "type": "object",
                "properties": {
                    "owner": {
                        "type": "string",
                        "description": "The owner of the repository"
                    },
                    "repo": {
                        "type": "string",
                        "description": "The name of the repository"
                    },
                    "issue_number": {
                        "type": "integer",
                        "description": "The issue number"
                    }
                },
                "required": ["owner", "repo", "issue_number"]
            }),
        ),
        // Calculator tool
        DeepSeekService::create_tool(
            "calculate".to_string(),
            Some("Perform a calculation".to_string()),
            json!({
                "type": "object",
                "properties": {
                    "expression": {
                        "type": "string",
                        "description": "The mathematical expression to evaluate"
                    }
                },
                "required": ["expression"]
            }),
        ),
    ]
}

pub fn configure_app() -> Router {
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

    let _repomap_service = Arc::new(RepomapService::new(
        env::var("FIRECRAWL_API_KEY").expect("FIRECRAWL_API_KEY must be set"),
    ));

    // Create available tools
    let tools = create_tools();

    // Create WebSocket state with services
    let ws_state = WebSocketState::new(tool_model, chat_model, github_service.clone(), tools);

    // Create the main router
    Router::new()
        // Main routes
        .route("/", get(routes::home))
        .route("/chat", get(routes::chat))
        .route("/onyx", get(routes::mobile_app))
        .route("/services", get(routes::business))
        .route("/video-series", get(routes::video_series))
        .route("/company", get(routes::company))
        .route("/coming-soon", get(routes::coming_soon))
        .route("/repomap", get(routes::repomap))
        // Auth routes
        .route("/login", get(routes::login))
        .route("/signup", get(routes::signup))
        // Static files
        .nest_service("/static", ServeDir::new("./static").precompressed_gzip())
        // Template files
        .nest_service(
            "/templates",
            ServeDir::new("./templates").precompressed_gzip(),
        )
        .with_state(ws_state)
}