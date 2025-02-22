use std::{collections::HashMap, sync::Arc};

use axum::extract::ws::{Message, WebSocket};
use futures_util::{SinkExt, StreamExt};
use tokio::sync::{mpsc, Mutex, RwLock};
use tracing::{debug, error, info};

use crate::server::{
    config::AppState,
    services::{github_issue::GitHubService, model_router::ModelRouter},
    ws::handlers::chat::{ChatDelta, ChatMessage, ChatResponse},
};

pub type WebSocketSender = mpsc::Sender<String>;

#[derive(Clone)]
pub struct WebSocketState {
    pub connections: Arc<RwLock<HashMap<String, WebSocketSender>>>,
    pub github_service: Arc<GitHubService>,
    pub model_router: Arc<ModelRouter>,
}

impl WebSocketState {
    pub fn new(github_service: Arc<GitHubService>, model_router: Arc<ModelRouter>) -> Self {
        info!("Creating new WebSocketState");
        Self {
            connections: Arc::new(RwLock::new(HashMap::new())),
            github_service,
            model_router,
        }
    }

    pub async fn send_to(
        &self,
        conn_id: &str,
        msg: &str,
    ) -> Result<(), Box<dyn std::error::Error + Send + Sync>> {
        debug!("Sending message to connection {}: {}", conn_id, msg);
        if let Some(tx) = self.connections.read().await.get(conn_id) {
            tx.send(msg.to_string()).await?;
            debug!("Message sent successfully");
        } else {
            debug!("Connection {} not found", conn_id);
        }
        Ok(())
    }

    pub async fn broadcast(
        &self,
        msg: &str,
    ) -> Result<(), Box<dyn std::error::Error + Send + Sync>> {
        debug!("Broadcasting message to all connections: {}", msg);
        let connections = self.connections.read().await;
        for (id, tx) in connections.iter() {
            debug!("Sending to connection {}", id);
            if let Err(e) = tx.send(msg.to_string()).await {
                error!("Failed to send to connection {}: {:?}", id, e);
            }
        }
        Ok(())
    }

    pub async fn add_connection(
        &self,
        conn_id: String,
        tx: WebSocketSender,
    ) -> Result<(), Box<dyn std::error::Error + Send + Sync>> {
        info!("Adding new connection: {}", conn_id);
        self.connections.write().await.insert(conn_id.clone(), tx);
        let count = self.connections.read().await.len();
        info!("Total active connections: {}", count);
        Ok(())
    }

    pub async fn remove_connection(
        &self,
        conn_id: &str,
    ) -> Result<(), Box<dyn std::error::Error + Send + Sync>> {
        info!("Removing connection: {}", conn_id);
        self.connections.write().await.remove(conn_id);
        let count = self.connections.read().await.len();
        info!("Total active connections: {}", count);
        Ok(())
    }
}

pub struct MessageProcessor {
    app_state: AppState,
    user_id: String,
    conn_id: String,
}

impl MessageProcessor {
    pub fn new(app_state: AppState, user_id: String, conn_id: String) -> Self {
        Self {
            app_state,
            user_id,
            conn_id,
        }
    }

    pub async fn process_message(
        &mut self,
        text: &str,
        tx: &Arc<mpsc::Sender<String>>,
    ) -> Result<(), Box<dyn std::error::Error + Send + Sync>> {
        let chat_msg: ChatMessage = match serde_json::from_str(text) {
            Ok(msg) => msg,
            Err(e) => {
                error!("Failed to parse message: {:?}", e);
                let error_msg = serde_json::to_string(&ChatResponse::Error {
                    message: format!("Invalid message format: {}", e),
                    connection_id: Some(self.conn_id.clone()),
                })?;
                tx.send(error_msg).await?;
                return Ok(());
            }
        };

        match chat_msg {
            ChatMessage::Subscribe {
                scope,
                connection_id,
                conversation_id,
                ..
            } => {
                // Update our connection ID to match the client's if provided
                if let Some(client_conn_id) = connection_id {
                    debug!(
                        "Updating connection ID from {} to {}",
                        self.conn_id, client_conn_id
                    );
                    self.conn_id = client_conn_id;
                }

                info!(
                    "Processing subscribe message for scope: {} (conversation: {:?})",
                    scope, conversation_id
                );
                let response = serde_json::to_string(&ChatResponse::Subscribed {
                    scope,
                    connection_id: Some(self.conn_id.clone()),
                    last_sync_id: 0,
                })?;
                tx.send(response).await?;
            }
            ChatMessage::Message {
                id,
                conversation_id,
                content,
                repos,
                use_reasoning,
                ..
            } => {
                info!(
                    "Processing chat message: id={}, conv={:?}, content={}",
                    id, conversation_id, content
                );

                // Create conversation ID if not provided
                let conversation_id = conversation_id.unwrap_or_else(uuid::Uuid::new_v4);

                // Send the message to the model router
                let response = match self
                    .app_state
                    .ws_state
                    .model_router
                    .chat(content, use_reasoning.unwrap_or(false))
                    .await
                {
                    Ok(response) => response,
                    Err(e) => {
                        error!("Failed to process message: {:?}", e);
                        let error_msg = serde_json::to_string(&ChatResponse::Error {
                            message: format!("Failed to process message: {}", e),
                            connection_id: Some(self.conn_id.clone()),
                        })?;
                        tx.send(error_msg).await?;
                        return Ok(());
                    }
                };

                // Send the response
                let (content, reasoning) = response;
                let response = serde_json::to_string(&ChatResponse::Update {
                    message_id: id,
                    connection_id: Some(self.conn_id.clone()),
                    delta: ChatDelta {
                        content: Some(content),
                        reasoning,
                    },
                })?;

                match tx.send(response).await {
                    Ok(_) => {
                        let complete_response = serde_json::to_string(&ChatResponse::Complete {
                            message_id: id,
                            connection_id: Some(self.conn_id.clone()),
                            conversation_id,
                        })?;
                        tx.send(complete_response).await?;
                    }
                    Err(e) => {
                        error!("Failed to send response: {:?}", e);
                        return Ok(());
                    }
                }
            }
        }
        Ok(())
    }
}

pub struct WebSocketTransport {
    pub state: Arc<WebSocketState>,
    pub app_state: AppState,
}

impl WebSocketTransport {
    pub fn new(state: Arc<WebSocketState>, app_state: AppState) -> Self {
        info!("Creating new WebSocketTransport");
        Self { state, app_state }
    }

    pub async fn handle_socket(
        &self,
        socket: WebSocket,
        user_id: String,
    ) -> Result<(), Box<dyn std::error::Error + Send + Sync>> {
        info!("Handling new WebSocket connection for user: {}", user_id);

        let (sender, mut receiver) = socket.split();
        let (tx, mut rx) = mpsc::channel::<String>(32);

        let conn_id = uuid::Uuid::new_v4().to_string();
        info!("Created connection ID: {}", conn_id);

        if let Err(e) = self.state.add_connection(conn_id.clone(), tx.clone()).await {
            error!("Failed to add connection: {:?}", e);
            let error_msg = serde_json::to_string(&ChatResponse::Error {
                message: "Failed to initialize connection".to_string(),
                connection_id: Some(conn_id.clone()),
            })?;
            tx.send(error_msg).await?;
            return Err(e);
        }

        // Clone AppState before moving into async task
        let app_state = self.app_state.clone();
        let receive_conn_id = conn_id.clone();
        let send_conn_id = conn_id.clone();
        let cleanup_state = self.state.clone();

        let is_closing = Arc::new(Mutex::new(false));
        let is_closing_send = is_closing.clone();
        let is_closing_receive = is_closing.clone();

        let (pending_tx, mut pending_rx) = mpsc::channel::<String>(32);
        let pending_tx = Arc::new(pending_tx);
        let pending_tx_receive = pending_tx.clone();

        let cleanup = {
            let cleanup_conn_id = conn_id;
            let cleanup_state = cleanup_state;
            move || async move {
                info!("Running cleanup for connection: {}", cleanup_conn_id);
                if let Err(e) = cleanup_state.remove_connection(&cleanup_conn_id).await {
                    error!("Failed to clean up connection: {:?}", e);
                }
                info!("Cleanup completed for connection: {}", cleanup_conn_id);
            }
        };

        let receive_handle = tokio::spawn(async move {
            info!("Starting receive task for connection: {}", receive_conn_id);
            let mut processor =
                MessageProcessor::new(app_state, user_id.clone(), receive_conn_id.clone());
            while let Some(Ok(msg)) = receiver.next().await {
                match msg {
                    Message::Text(ref text) => {
                        info!("Received message on {}: {}", receive_conn_id, text);
                        if let Err(e) = processor.process_message(text, &pending_tx_receive).await {
                            error!("Error processing message on {}: {:?}", receive_conn_id, e);
                            let error_msg = serde_json::to_string(&ChatResponse::Error {
                                message: format!("Message processing error: {}", e),
                                connection_id: Some(receive_conn_id.clone()),
                            })
                            .unwrap();
                            if let Err(e) = pending_tx_receive.send(error_msg).await {
                                error!("Failed to send error message: {:?}", e);
                            }
                            break;
                        }
                    }
                    Message::Close(_) => {
                        info!("Client requested close on {}", receive_conn_id);
                        let mut is_closing = is_closing_receive.lock().await;
                        *is_closing = true;
                        break;
                    }
                    _ => {
                        debug!("Ignoring non-text message on {}", receive_conn_id);
                    }
                }
            }
            info!("Receive task ending for {}", receive_conn_id);
        });

        let send_handle = tokio::spawn(async move {
            info!("Starting send task for connection: {}", send_conn_id);
            let mut sender = sender;

            loop {
                tokio::select! {
                    Some(msg) = rx.recv() => {
                        debug!("Sending message on {}: {}", send_conn_id, msg);
                        if let Err(e) = pending_tx.send(msg).await {
                            error!("Failed to queue message: {:?}", e);
                            break;
                        }
                    }
                    Some(msg) = pending_rx.recv() => {
                        debug!("Processing pending message on {}: {}", send_conn_id, msg);
                        if let Err(e) = sender.send(Message::Text(msg)).await {
                            error!("Error sending message on {}: {:?}", send_conn_id, e);
                            break;
                        }
                    }
                    else => {
                        let is_closing = is_closing_send.lock().await;
                        if *is_closing && rx.is_empty() && pending_rx.is_empty() {
                            info!("All messages sent, closing connection {}", send_conn_id);
                            break;
                        }
                    }
                }
            }
            info!("Send task ending for {}", send_conn_id);
        });

        let (receive_result, send_result) = tokio::join!(receive_handle, send_handle);

        if let Err(e) = receive_result {
            error!("Receive task error: {:?}", e);
        }
        if let Err(e) = send_result {
            error!("Send task error: {:?}", e);
        }

        cleanup().await;

        Ok(())
    }
}
