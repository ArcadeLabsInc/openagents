use anyhow::Result;
use std::pin::Pin;
use futures_util::{Stream, StreamExt};
use tracing::info;
use tokio_stream::wrappers::ReceiverStream;
use crate::server::services::deepseek::StreamUpdate;

pub struct PlanningContext {
    pub llm_service: crate::server::services::deepseek::DeepSeekService,
}

impl PlanningContext {
    pub fn new(ollama_url: &str) -> Result<Self> {
        Ok(Self {
            llm_service: crate::server::services::deepseek::DeepSeekService::new(ollama_url.to_string()),
        })
    }

    pub async fn generate_plan(
        &self,
        issue_number: i32,
        title: &str,
        description: &str,
        repo_map: &str,
        file_context: &str,
    ) -> Result<Pin<Box<dyn Stream<Item = Result<String, anyhow::Error>> + Send>>> {
        let prompt = format!(
            r#"Generate a plan for issue #{} based on:
Title: {}
Description: {}
Repository Map: {}
File Context: {}"#,
            issue_number, title, description, repo_map, file_context
        );

        info!("Generating plan with prompt: {}", prompt);
        let receiver = self.llm_service.chat_stream(prompt, true).await;
        
        // Convert receiver into a Stream
        let stream = ReceiverStream::new(receiver)
            .map(|update| match update {
                StreamUpdate::Content(content) => Ok(content),
                StreamUpdate::Reasoning(reasoning) => Ok(reasoning),
                StreamUpdate::Done => Ok("".to_string()),
                StreamUpdate::ToolCalls(_) => Ok("".to_string()), // Ignore tool calls in planning
            });
            
        Ok(Box::pin(stream))
    }
}

#[cfg(test)]
mod tests {
    use super::*;
    use mockito::Server;
    use serde_json::json;

    #[tokio::test]
    async fn test_validate_llm_response() {
        let mut server = Server::new();
        let mock_response = json!({
            "choices": [{
                "message": {
                    "content": "feat: add multiply function"
                }
            }]
        });

        let mock = server.mock("POST", "/v1/chat/completions")
            .with_status(200)
            .with_header("content-type", "application/json")
            .with_body(mock_response.to_string())
            .create();

        std::env::set_var("DEEPSEEK_API_URL", &server.url());
        
        let context = PlanningContext::new("test_url").unwrap();
        let mut stream = context.generate_plan(
            123,
            "Add multiply function",
            "Add a multiply function",
            "src/main.rs",
            "test context",
        ).await.unwrap();

        let mut response = String::new();
        while let Some(chunk) = stream.next().await {
            response.push_str(&chunk.unwrap());
        }

        mock.assert();
        assert!(!response.is_empty());
    }
}