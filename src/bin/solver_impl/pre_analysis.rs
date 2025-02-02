use anyhow::Result;
use openagents::server::services::deepseek::{DeepSeekService, StreamUpdate};
use openagents::solver::state::{SolverState, SolverStatus};
use std::collections::HashSet;
use std::io::Write;
use tracing::{debug, info};

pub async fn analyze_with_deepseek(
    state: &mut SolverState,
    deepseek: &DeepSeekService,
    valid_paths: &HashSet<String>,
) -> Result<(String, String)> {
    info!("Starting DeepSeek pre-analysis...");
    state.update_status(SolverStatus::Thinking);

    let prompt = format!(
        "You are a code analysis expert. Based on this analysis, think through which files would need to be modified. \
        Show your reasoning process step by step.\n\n\
        IMPORTANT: You MUST ONLY consider paths from this list:\n{}\n\n\
        Analysis:\n{}\n\n\
        Think through this step by step, explaining your reasoning. \
        At the end, provide a final conclusion listing the most relevant files (up to 10) with their relevance scores (1-10, where 10 is most relevant) \
        and reasons for modification.",
        valid_paths.iter().map(|s| s.as_str()).collect::<Vec<_>>().join("\n"),
        state.analysis
    );

    debug!("Sending prompt to DeepSeek...");
    let mut stream = deepseek.chat_stream(prompt, true).await;
    let mut full_response = String::new();
    let mut reasoning = String::new();
    let mut current_reasoning = String::new();

    println!("\nThinking process:\n");
    while let Some(update) = stream.recv().await {
        match update {
            StreamUpdate::Content(content) => {
                print!("{}", content);
                std::io::stdout().flush()?;
                full_response.push_str(&content);
            }
            StreamUpdate::Reasoning(r) => {
                current_reasoning.push_str(&r);
                // Only print when we have a complete sentence or thought
                if r.ends_with('.') || r.ends_with('!') || r.ends_with('?') || r.ends_with('\n') {
                    println!("🤔 {}", current_reasoning);
                    reasoning.push_str(&current_reasoning);
                    current_reasoning.clear();
                }
            }
            StreamUpdate::Done => break,
            _ => {}
        }
    }

    // Handle any remaining reasoning content
    if !current_reasoning.is_empty() {
        println!("🤔 {}", current_reasoning);
        reasoning.push_str(&current_reasoning);
    }

    println!("\nDeepSeek analysis complete.\n");
    debug!("Full response: {}", full_response);
    debug!("Reasoning trace: {}", reasoning);

    Ok((full_response, reasoning))
}