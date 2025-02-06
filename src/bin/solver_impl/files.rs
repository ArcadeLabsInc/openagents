use crate::solver_impl::types::RelevantFiles;
use anyhow::Result;
use openagents::server::services::gemini::GeminiService;
use openagents::solver::state::{SolverState, SolverStatus};
use std::collections::HashSet;
use tracing::{debug, error, info};

pub async fn identify_files(
    state: &mut SolverState,
    gemini: &GeminiService,
    valid_paths: &HashSet<String>,
) -> Result<()> {
    info!("Starting file identification process...");
    state.update_status(SolverStatus::Thinking);

    // Convert HashSet to Vec for Gemini API
    let valid_paths_vec: Vec<String> = valid_paths.iter().cloned().collect();

    // Get Gemini's analysis with streaming
    let mut stream = gemini
        .analyze_files_stream(
            &state.analysis,
            &valid_paths_vec,
            &state.repo_context,
        )
        .await;

    let mut full_response = String::new();
    while let Some(update) = stream.recv().await {
        match update {
            openagents::server::services::gemini::StreamUpdate::Content(content) => {
                full_response.push_str(&content);
                debug!("Received content: {}", content);
            }
            openagents::server::services::gemini::StreamUpdate::Done => {
                debug!("Stream complete");
                break;
            }
        }
    }

    // Parse response into RelevantFiles
    let json_str = if let Some(start) = full_response.find('{') {
        if let Some(end) = full_response.rfind('}') {
            &full_response[start..=end]
        } else {
            return Err(anyhow::anyhow!("Invalid JSON in response - no closing brace"));
        }
    } else {
        return Err(anyhow::anyhow!("Invalid JSON in response - no opening brace"));
    };

    let relevant_files: RelevantFiles = serde_json::from_str(json_str)?;

    // Add files to state, ensuring paths are relative and valid
    for mut file in relevant_files.files {
        // Remove leading slash if present
        if file.path.starts_with('/') {
            file.path = file.path[1..].to_string();
        }

        // Only add if path is in valid_paths
        if valid_paths.contains(&file.path) {
            debug!("Adding valid file: {}", file.path);
            // Convert relevance score from 1-10 to 0-1 for state storage
            let normalized_score = file.relevance_score / 10.0;
            state.add_file(file.path, file.reason, normalized_score);
        } else {
            error!("Skipping invalid file path: {}", file.path);
        }
    }

    Ok(())
}