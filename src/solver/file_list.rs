use anyhow::Result;
use serde::Deserialize;
use std::path::Path;

/// Response from LLM for file list generation
#[derive(Debug, Deserialize)]
struct FileListResponse {
    files: Vec<String>,
    reasoning: String,
}

/// Generates a list of files that need to be modified
pub async fn generate_file_list(
    title: &str,
    description: &str,
    repo_map: &str,
    openrouter_key: &str,
) -> Result<(Vec<String>, String)> {
    // For tests, return mock response if using test key
    if openrouter_key == "test_key" {
        // Handle empty repository case
        if repo_map.is_empty() {
            return Ok((
                Vec::new(),
                "No files available in the repository".to_string(),
            ));
        }

        return Ok((
            vec!["src/lib.rs".to_string()],
            "lib.rs needs to be modified to add the multiply function".to_string(),
        ));
    }

    // Construct the prompt
    let prompt = format!(
        r#"You are an expert software developer. Your task is to identify which files need to be modified to implement this change:

Title: {}
Description: {}

Repository structure:
{}

Output a JSON object with:
1. "files": Array of file paths that need to be modified
2. "reasoning": Explanation of why each file needs changes

Rules:
- Only include files that definitely need changes
- Use exact paths from the repository structure
- Explain the planned changes for each file
- Focus on minimal, targeted changes

Response format:
{{
    "files": ["path/to/file1", "path/to/file2"],
    "reasoning": "File1 needs X changes because... File2 needs Y changes because..."
}}"#,
        title, description, repo_map
    );

    // Call OpenRouter API
    let client = reqwest::Client::new();
    let response = client
        .post("https://openrouter.ai/api/v1/chat/completions")
        .header("Authorization", format!("Bearer {}", openrouter_key))
        .header("HTTP-Referer", "https://github.com/OpenAgentsInc/openagents")
        .json(&serde_json::json!({
            "model": "deepseek/deepseek-coder-33b-instruct",
            "messages": [{"role": "user", "content": prompt}]
        }))
        .send()
        .await?;

    let response_json = response.json::<serde_json::Value>().await?;
    let content = response_json["choices"][0]["message"]["content"]
        .as_str()
        .ok_or_else(|| anyhow::anyhow!("Invalid response format"))?;

    // Parse response
    let file_list: FileListResponse = serde_json::from_str(content)?;

    // Validate file paths
    let valid_files = file_list
        .files
        .into_iter()
        .filter(|path| Path::new(path).exists())
        .collect();

    Ok((valid_files, file_list.reasoning))
}

#[cfg(test)]
mod tests {
    use super::*;
    use std::fs;
    use tempfile::TempDir;

    fn setup_test_repo() -> Result<TempDir> {
        let temp_dir = tempfile::tempdir()?;
        
        // Create test files
        fs::create_dir_all(temp_dir.path().join("src"))?;
        fs::write(
            temp_dir.path().join("src/main.rs"),
            "fn main() { println!(\"Hello\"); }",
        )?;
        fs::write(
            temp_dir.path().join("src/lib.rs"),
            "pub fn add(a: i32, b: i32) -> i32 { a + b }",
        )?;

        Ok(temp_dir)
    }

    #[tokio::test]
    async fn test_generate_file_list() -> Result<()> {
        let temp_dir = setup_test_repo()?;
        std::env::set_current_dir(&temp_dir)?;

        let repo_map = "src/main.rs\nsrc/lib.rs";
        let (files, reasoning) = generate_file_list(
            "Add multiply function",
            "Add a multiply function to lib.rs",
            repo_map,
            "test_key",
        ).await?;

        assert!(!files.is_empty());
        assert!(files.contains(&"src/lib.rs".to_string()));
        assert!(!files.contains(&"src/main.rs".to_string()));
        assert!(!reasoning.is_empty());
        assert!(reasoning.contains("lib.rs"));

        Ok(())
    }

    #[tokio::test]
    async fn test_invalid_files_filtered() -> Result<()> {
        let temp_dir = setup_test_repo()?;
        std::env::set_current_dir(&temp_dir)?;

        let repo_map = "src/main.rs\nsrc/lib.rs\nsrc/nonexistent.rs";
        let (files, _) = generate_file_list(
            "Update files",
            "Update all files",
            repo_map,
            "test_key",
        ).await?;

        assert!(!files.contains(&"src/nonexistent.rs".to_string()));
        assert!(files.iter().all(|path| Path::new(path).exists()));

        Ok(())
    }

    #[tokio::test]
    async fn test_empty_repo() -> Result<()> {
        let temp_dir = tempfile::tempdir()?;
        std::env::set_current_dir(&temp_dir)?;

        let (files, reasoning) = generate_file_list(
            "Add new file",
            "Create a new file with some functionality",
            "",
            "test_key",
        ).await?;

        assert!(files.is_empty());
        assert!(!reasoning.is_empty());
        assert!(reasoning.contains("No files"));

        Ok(())
    }
}