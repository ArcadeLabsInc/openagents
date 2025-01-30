use crate::server::services::gateway::{types::GatewayMetadata, Gateway};
use anyhow::Result;
use futures_util::{Stream, StreamExt};
use std::pin::Pin;
use tokio::sync::mpsc;
use tokio_stream::wrappers::ReceiverStream;

pub struct OllamaService {
    config: super::config::OllamaConfig,
}

impl OllamaService {
    pub fn new() -> Self {
        Self {
            config: super::config::OllamaConfig::global().clone(),
        }
    }

    pub fn with_config(base_url: &str, model: &str) -> Self {
        Self {
            config: super::config::OllamaConfig {
                base_url: base_url.to_string(),
                model: model.to_string(),
            },
        }
    }
}

#[async_trait::async_trait]
impl Gateway for OllamaService {
    fn metadata(&self) -> GatewayMetadata {
        GatewayMetadata {
            name: "ollama".to_string(),
            openai_compatible: false,
            supported_features: vec!["chat".to_string(), "streaming".to_string()],
            default_model: self.config.model.clone(),
            available_models: vec![self.config.model.clone()],
        }
    }

    async fn chat(&self, prompt: String, _use_reasoner: bool) -> Result<(String, Option<String>)> {
        let client = reqwest::Client::new();
        let response = client
            .post(format!("{}/api/chat", self.config.base_url))
            .json(&serde_json::json!({
                "model": self.config.model,
                "messages": [{
                    "role": "user",
                    "content": prompt
                }],
                "stream": false
            }))
            .send()
            .await?;

        let response_json = response.json::<serde_json::Value>().await?;
        let content = response_json["message"]["content"]
            .as_str()
            .ok_or_else(|| anyhow::anyhow!("Invalid response format from Ollama"))?;

        Ok((content.to_string(), None))
    }

    async fn chat_stream(
        &self,
        prompt: String,
        _use_reasoner: bool,
    ) -> Result<Pin<Box<dyn Stream<Item = Result<String>> + Send>>> {
        let (tx, rx) = mpsc::channel(100);
        let client = reqwest::Client::new();
        let config = self.config.clone();

        tokio::spawn(async move {
            let response = client
                .post(format!("{}/api/chat", config.base_url))
                .json(&serde_json::json!({
                    "model": config.model,
                    "messages": [{
                        "role": "user",
                        "content": prompt
                    }],
                    "stream": true
                }))
                .send()
                .await;

            match response {
                Ok(response) => {
                    let mut stream = response.bytes_stream();
                    while let Some(chunk) = stream.next().await {
                        match chunk {
                            Ok(bytes) => {
                                if let Ok(text) = String::from_utf8(bytes.to_vec()) {
                                    if let Ok(json) = serde_json::from_str::<serde_json::Value>(&text) {
                                        if let Some(content) = json["message"]["content"].as_str() {
                                            let _ = tx.send(Ok(content.to_string())).await;
                                        }
                                        if json["done"].as_bool().unwrap_or(false) {
                                            break;
                                        }
                                    }
                                }
                            }
                            Err(e) => {
                                let _ = tx.send(Err(anyhow::anyhow!(e))).await;
                                break;
                            }
                        }
                    }
                }
                Err(e) => {
                    let _ = tx.send(Err(anyhow::anyhow!(e))).await;
                }
            }
        });

        Ok(Box::pin(ReceiverStream::new(rx)))
    }
}