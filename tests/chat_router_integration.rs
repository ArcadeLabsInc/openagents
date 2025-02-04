use std::sync::Arc;

use axum::extract::ws::Message;
use openagents::server::{
    services::{
        deepseek::DeepSeekService,
        github_issue::GitHubService,
    },
    tools::create_tools,
    ws::{
        handlers::{chat::ChatHandler, MessageHandler},
        types::ChatMessage,
        transport::WebSocketState,
    },
};
use tracing_subscriber;

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

#[tokio::test]
async fn test_chat_router_integration() {
    init_logging();

    // Create test services
    let tool_model = Arc::new(DeepSeekService::new("test_key".to_string()));
    let chat_model = Arc::new(DeepSeekService::new("test_key".to_string()));
    let github_service = Arc::new(
        GitHubService::new(Some("test_token".to_string())).expect("Failed to create GitHub service"),
    );

    // Create tools
    let tools = create_tools();

    // Create WebSocket state
    let ws_state = WebSocketState::new(
        tool_model.clone(),
        chat_model.clone(),
        github_service.clone(),
        tools,
    );
    let ws_state = Arc::new(ws_state);

    // Add test connection
    let mut rx = ws_state.add_test_connection("test_conn", 1).await;

    // Create chat handler
    let chat_handler = ChatHandler::new(ws_state.clone(), github_service.clone());

    // Test message handling
    let msg = ChatMessage::UserMessage {
        content: "Hello, world!".to_string(),
    };

    chat_handler.handle_message(msg, "test_conn".to_string()).await.unwrap();

    // Check response
    if let Ok(response) = rx.try_recv() {
        match response {
            Message::Text(text) => {
                let response: serde_json::Value = serde_json::from_str(&text).unwrap();
                assert_eq!(response["type"], "assistant");
                assert!(response["content"].is_string());
            }
            _ => panic!("Expected text message"),
        }
    } else {
        panic!("No response received");
    }
}

#[tokio::test]
async fn test_chat_router_streaming() {
    init_logging();

    // Create test services
    let tool_model = Arc::new(DeepSeekService::new("test_key".to_string()));
    let chat_model = Arc::new(DeepSeekService::new("test_key".to_string()));
    let github_service = Arc::new(
        GitHubService::new(Some("test_token".to_string())).expect("Failed to create GitHub service"),
    );

    // Create tools
    let tools = create_tools();

    // Create WebSocket state
    let ws_state = WebSocketState::new(
        tool_model.clone(),
        chat_model.clone(),
        github_service.clone(),
        tools,
    );
    let ws_state = Arc::new(ws_state);

    // Add test connection
    let mut rx = ws_state.add_test_connection("test_conn", 1).await;

    // Create chat handler
    let chat_handler = ChatHandler::new(ws_state.clone(), github_service.clone());

    // Test streaming message
    let msg = ChatMessage::UserMessage {
        content: "Stream this response".to_string(),
    };

    chat_handler.handle_message(msg, "test_conn".to_string()).await.unwrap();

    // Check streaming responses
    let mut responses = Vec::new();
    while let Ok(response) = rx.try_recv() {
        match response {
            Message::Text(text) => {
                let response: serde_json::Value = serde_json::from_str(&text).unwrap();
                assert_eq!(response["type"], "assistant");
                responses.push(response);
            }
            _ => panic!("Expected text message"),
        }
    }

    assert!(!responses.is_empty(), "No streaming responses received");
}