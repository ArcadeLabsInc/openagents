use axum::{
    extract::{Json, Path, State},
    http::StatusCode,
};
use axum_extra::extract::cookie::CookieJar;
use serde::{Deserialize, Serialize};
use serde_json::json;
use tracing::{error, info};
use uuid::Uuid;

use crate::server::{
    config::AppState,
    handlers::oauth::session::SESSION_COOKIE_NAME,
    models::chat::{CreateConversationRequest, CreateMessageRequest, Message},
    services::{chat_database::ChatDatabaseService, gateway::Gateway},
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
    cookies: CookieJar,
    State(state): State<AppState>,
    Json(request): Json<StartRepoChatRequest>,
) -> Result<Json<StartChatResponse>, (StatusCode, String)> {
    info!("Starting repo chat with request: {:?}", request);

    // Create chat database service
    let chat_db = ChatDatabaseService::new(state.pool);

    // Get user info from session
    let user_id = if let Some(session_cookie) = cookies.get(SESSION_COOKIE_NAME) {
        session_cookie.value().to_string()
    } else {
        return Err((
            StatusCode::UNAUTHORIZED,
            "No session found. Please log in.".to_string(),
        ));
    };
    info!("Using user_id: {}", user_id);

    // Create conversation
    let conversation = chat_db
        .create_conversation(&CreateConversationRequest {
            user_id: user_id.clone(),
            title: Some(format!("Repo chat: {}", request.message)),
        })
        .await
        .map_err(|e| {
            error!("Failed to create conversation: {:?}", e);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to create conversation: {}", e),
            )
        })?;

    info!("Created conversation with id: {}", conversation.id);

    // Create initial message with repos metadata
    let message = chat_db
        .create_message(&CreateMessageRequest {
            conversation_id: conversation.id,
            user_id: user_id.clone(),
            role: "user".to_string(),
            content: request.message.clone(),
            metadata: Some(json!({
                "repos": request.repos
            })),
            tool_calls: None,
        })
        .await
        .map_err(|e| {
            error!("Failed to create message: {:?}", e);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to create message: {}", e),
            )
        })?;

    info!("Created message with id: {}", message.id);

    // Convert message to Groq format
    let _messages = vec![json!({
        "role": "user",
        "content": request.message
    })];

    // Get Groq response
    let (ai_response, _) = state.groq.chat(request.message, false).await.map_err(|e| {
        error!("Failed to get Groq response: {:?}", e);
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            format!("Failed to get AI response: {}", e),
        )
    })?;

    // Save AI response
    let ai_message = chat_db
        .create_message(&CreateMessageRequest {
            conversation_id: conversation.id,
            user_id: user_id.clone(),
            role: "assistant".to_string(),
            content: ai_response,
            metadata: Some(json!({
                "repos": request.repos
            })),
            tool_calls: None,
        })
        .await
        .map_err(|e| {
            error!("Failed to save AI response: {:?}", e);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to save AI response: {}", e),
            )
        })?;

    info!("Created AI message with id: {}", ai_message.id);

    Ok(Json(StartChatResponse {
        id: conversation.id.to_string(),
        initial_message: message.content,
    }))
}

pub async fn send_message(
    cookies: CookieJar,
    State(state): State<AppState>,
    Json(request): Json<SendMessageRequest>,
) -> Result<Json<SendMessageResponse>, (StatusCode, String)> {
    info!("Sending message with request: {:?}", request);

    // Create chat database service
    let chat_db = ChatDatabaseService::new(state.pool);

    // Get user info from session
    let user_id = if let Some(session_cookie) = cookies.get(SESSION_COOKIE_NAME) {
        session_cookie.value().to_string()
    } else {
        return Err((
            StatusCode::UNAUTHORIZED,
            "No session found. Please log in.".to_string(),
        ));
    };

    // Create user message
    let _message = chat_db
        .create_message(&CreateMessageRequest {
            conversation_id: request.conversation_id,
            user_id: user_id.clone(),
            role: "user".to_string(),
            content: request.message.clone(),
            metadata: request.repos.clone().map(|repos| json!({ "repos": repos })),
            tool_calls: None,
        })
        .await
        .map_err(|e| {
            error!("Failed to create message: {:?}", e);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to create message: {}", e),
            )
        })?;

    // Get AI response
    let (ai_response, _) = state.groq.chat(request.message, false).await.map_err(|e| {
        error!("Failed to get Groq response: {:?}", e);
        (
            StatusCode::INTERNAL_SERVER_ERROR,
            format!("Failed to get AI response: {}", e),
        )
    })?;

    // Save AI response
    let ai_message = chat_db
        .create_message(&CreateMessageRequest {
            conversation_id: request.conversation_id,
            user_id: user_id,
            role: "assistant".to_string(),
            content: ai_response.clone(),
            metadata: request.repos.clone().map(|repos| json!({ "repos": repos })),
            tool_calls: None,
        })
        .await
        .map_err(|e| {
            error!("Failed to save AI response: {:?}", e);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to save AI response: {}", e),
            )
        })?;

    Ok(Json(SendMessageResponse {
        id: ai_message.id.to_string(),
        message: ai_response,
    }))
}

pub async fn get_conversation_messages(
    cookies: CookieJar,
    State(state): State<AppState>,
    Path(conversation_id): Path<Uuid>,
) -> Result<Json<Vec<Message>>, (StatusCode, String)> {
    // Create chat database service
    let chat_db = ChatDatabaseService::new(state.pool);

    // Get user info from session
    let user_id = if let Some(session_cookie) = cookies.get(SESSION_COOKIE_NAME) {
        session_cookie.value().to_string()
    } else {
        return Err((
            StatusCode::UNAUTHORIZED,
            "No session found. Please log in.".to_string(),
        ));
    };

    // Get messages
    let messages = chat_db
        .get_conversation_messages(conversation_id)
        .await
        .map_err(|e| {
            error!("Failed to get messages: {:?}", e);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                format!("Failed to get messages: {}", e),
            )
        })?;

    // Verify user has access to this conversation
    if let Some(first_message) = messages.first() {
        if first_message.user_id != user_id {
            return Err((
                StatusCode::FORBIDDEN,
                "You do not have access to this conversation".to_string(),
            ));
        }
    }

    Ok(Json(messages))
}
