use anyhow::Result;
use std::time::Duration;
use tokio::time::timeout;
use crate::server::services::{
    gateway::Gateway,
    openrouter::OpenRouterService,
    StreamUpdate,
};
use crate::solver::display::{print_colored, flush_stdout};
use termcolor::Color;

pub struct PlanningContext {
    service: OpenRouterService,
}

impl PlanningContext {
    pub fn new() -> Result<Self> {
        let service = OpenRouterService::new()?;
        Ok(Self { service })
    }

    pub async fn generate_plan(&self, issue_number: i32, title: &str, body: &str, map: &str) -> Result<String> {
        let plan_prompt = format!(
            "You are a Rust development expert. Analyze this GitHub issue and repository map to create an implementation plan.\n\n\
            Issue #{}: {}\n{}\n\nRepository map:\n{}\n\n\
            Create a detailed implementation plan including:\n\
            1. Files that need to be created or modified\n\
            2. Key functionality to implement\n\
            3. Required dependencies or imports\n\
            4. Testing strategy\n\
            Be specific and focus on practical implementation details.",
            issue_number,
            title,
            body,
            map
        );

        print_colored("\nGenerating Implementation Plan:\n", Color::Yellow)?;
        println!(
            "Sending prompt to OpenRouter ({} chars)...",
            plan_prompt.len()
        );

        let mut implementation_plan = String::new();
        let mut in_reasoning = true;
        let mut stream = self.service.chat_stream(plan_prompt.clone(), true).await;

        println!("Waiting for OpenRouter response...");

        // Set a longer timeout for the entire stream processing
        let stream_timeout = Duration::from_secs(180); // 3 minutes
        match timeout(stream_timeout, async {
            while let Some(update) = stream.recv().await {
                match update {
                    StreamUpdate::Reasoning(r) => {
                        let _ = print_colored(&r, Color::Yellow);
                        let _ = flush_stdout();
                    }
                    StreamUpdate::Content(c) => {
                        if in_reasoning {
                            println!();
                            let _ = print_colored("\nImplementation Plan:\n", Color::Green);
                            in_reasoning = false;
                        }
                        print!("{}", c);
                        implementation_plan.push_str(&c);
                        let _ = flush_stdout();
                    }
                    StreamUpdate::Done => {
                        println!("\nOpenRouter response complete");
                        break;
                    }
                    _ => {
                        println!("Received other update type");
                    }
                }
            }
        })
        .await
        {
            Ok(_) => {
                println!("\nStream processing completed successfully");
            }
            Err(_) => {
                print_colored("\nTimeout waiting for OpenRouter response\n", Color::Red)?;
            }
        }

        if implementation_plan.is_empty() {
            print_colored("\nWARNING: No implementation plan generated!\n", Color::Red)?;
            print_colored("\nTrying non-streaming API...\n", Color::Yellow)?;

            match self.service.chat(plan_prompt, true).await {
                Ok((content, reasoning)) => {
                    if let Some(r) = reasoning {
                        print_colored("\nReasoning:\n", Color::Yellow)?;
                        println!("{}", r);
                    }
                    print_colored("\nImplementation Plan:\n", Color::Green)?;
                    println!("{}", content);
                    implementation_plan = content;
                }
                Err(e) => {
                    print_colored(&format!("\nOpenRouter API error: {}\n", e), Color::Red)?;
                }
            }
        }

        Ok(implementation_plan)
    }
}