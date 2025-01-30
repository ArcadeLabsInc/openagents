use openagents::server::services::gateway::{Gateway, GatewayMetadata, StreamUpdate};
use openagents::server::services::ollama::service::OllamaService;
use std::env;
use tokio::sync::mpsc;

#[tokio::test]
async fn test_ollama_metadata() {
    let service = OllamaService::new();
    let metadata = service.metadata();
    assert_eq!(metadata.name, "ollama");
    assert_eq!(metadata.description, "Local model execution via Ollama");
}

#[tokio::test]
async fn test_ollama_chat() {
    let service = OllamaService::new();
    let (response, _) = service.chat("Test message".to_string(), false).await.unwrap();
    assert!(!response.is_empty());
}

#[tokio::test]
async fn test_ollama_chat_stream() {
    let service = OllamaService::new();
    let mut rx: mpsc::Receiver<StreamUpdate> = service.chat_stream("Test message".to_string(), false).await;
    
    let mut saw_content = false;
    while let Some(update) = rx.recv().await {
        match update {
            StreamUpdate::Content(content) => {
                assert!(!content.is_empty());
                saw_content = true;
            }
            StreamUpdate::Done => break,
            StreamUpdate::Error(e) => panic!("Unexpected error: {}", e),
        }
    }
    assert!(saw_content);
}

#[tokio::test]
async fn test_ollama_with_config() {
    let service = OllamaService::with_config("http://localhost:11434", "llama2");
    let metadata = service.metadata();
    assert_eq!(metadata.model, Some("llama2".to_string()));
}

#[tokio::test]
async fn test_ollama_error_handling() {
    let service = OllamaService::with_config("http://invalid-url", "invalid-model");
    let result = service.chat("Test message".to_string(), false).await;
    assert!(result.is_err());
}