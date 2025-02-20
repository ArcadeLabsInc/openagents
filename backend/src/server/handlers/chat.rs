use axum::{
    extract::{Json, State},
    http::StatusCode,
};
use serde::{Deserialize, Serialize};
use serde_json::json;
use uuid::Uuid;

use crate::server::{
    config::AppState,
    models::chat::{CreateConversationRequest, CreateMessageRequest},
    services::chat_database::ChatDatabaseService,
    handlers::oauth::session::get_user_id_from_session,
};

#[derive(Debug, Deserialize)]
pub struct StartRepoChatRequest {
    pub id: Uuid,
    pub message: String,
    pub repos: Vec<String>,
    pub scope: String,
}

#[derive(Debug, Deserialize)]
pub struct SendMessageRequest {
    pub conversation_id: Uuid,
    pub message: String,
    pub repos: Option<Vec<String>>,
}

#[derive(Debug, Serialize)]
pub struct StartChatResponse {
    pub id: String,
    pub initial_message: String,
}

#[derive(Debug, Serialize)]
pub struct SendMessageResponse {
    pub id: String,
    pub message: String,
}

pub async fn start_repo_chat(
    State(state): State<AppState>,
    Json(request): Json<StartRepoChatRequest>,
) -> Result<Json<StartChatResponse>, (StatusCode, String)> {
    // Create chat database service
    let chat_db = ChatDatabaseService::new(state.pool);

    // Get user info from session
    let user_id = get_user_id_from_session().unwrap_or("anonymous".to_string());

    // Create conversation
    let conversation = chat_db
        .create_conversation(&CreateConversationRequest {
            user_id: user_id.clone(),
            title: Some(format!("Repo chat: {}", request.message)),
        })
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to create conversation: {}", e),
            )
        })?;

    // Create initial message with repos metadata
    let message = chat_db
        .create_message(&CreateMessageRequest {
            conversation_id: conversation.id,
            user_id,
            role: "user".to_string(),
            content: request.message.clone(),
            metadata: Some(json!({
                "repos": request.repos
            })),
            tool_calls: None,
        })
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to create message: {}", e),
            )
        })?;

    Ok(Json(StartChatResponse {
        id: conversation.id.to_string(),
        initial_message: message.content,
    }))
}

pub async fn send_message(
    State(state): State<AppState>,
    Json(request): Json<SendMessageRequest>,
) -> Result<Json<SendMessageResponse>, (StatusCode, String)> {
    // Create chat database service
    let chat_db = ChatDatabaseService::new(state.pool);

    // Get user info from session
    let user_id = get_user_id_from_session().unwrap_or("anonymous".to_string());

    // Create message
    let message = chat_db
        .create_message(&CreateMessageRequest {
            conversation_id: request.conversation_id,
            user_id,
            role: "user".to_string(),
            content: request.message.clone(),
            metadata: request.repos.map(|repos| json!({ "repos": repos })),
            tool_calls: None,
        })
        .await
        .map_err(|e| {
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to create message: {}", e),
            )
        })?;

    Ok(Json(SendMessageResponse {
        id: message.id.to_string(),
        message: message.content,
    }))
}