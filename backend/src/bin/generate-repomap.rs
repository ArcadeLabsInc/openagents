use anyhow::{bail, Result};
use clap::Parser;
use dotenvy::dotenv;
use openagents::repo::{cleanup_temp_dir, clone_repository, RepoContext};
use openagents::repomap::generate_repo_map;
use std::env;
use std::fs;
use std::process::Command;

#[derive(Parser)]
#[command(author, version, about, long_about = None)]
struct Cli {
    /// Specify which branch to map
    #[arg(short, long)]
    branch: Option<String>,
}

fn get_current_branch() -> Option<String> {
    let output = Command::new("git")
        .args(["rev-parse", "--abbrev-ref", "HEAD"])
        .output()
        .ok()?;

    if output.status.success() {
        String::from_utf8(output.stdout)
            .ok()
            .map(|s| s.trim().to_string())
    } else {
        None
    }
}

#[tokio::main]
async fn main() -> Result<()> {
    let cli = Cli::parse();

    // Determine which branch to use
    let branch = cli
        .branch
        .or_else(get_current_branch)
        .unwrap_or_else(|| "main".to_string());
    println!("Using branch: {}", branch);

    // Load .env file first
    if let Err(e) = dotenv() {
        bail!("Failed to load .env file: {}", e);
    }

    // Get API keys immediately and fail if not present
    let api_key = env::var("DEEPSEEK_API_KEY")
        .map_err(|_| anyhow::anyhow!("DEEPSEEK_API_KEY not found in environment or .env file"))?;
    let github_token = env::var("GITHUB_TOKEN").ok();

    // Define the temporary directory path
    let temp_dir = env::temp_dir().join("rust_app_temp");

    // Clean up any existing temp directory first
    cleanup_temp_dir(&temp_dir);

    // Create the temporary directory
    std::fs::create_dir_all(&temp_dir)
        .map_err(|e| anyhow::anyhow!("Failed to create temporary directory: {}", e))?;
    println!("Temporary directory created at: {:?}", temp_dir);

    // Create context
    let ctx = RepoContext::new(temp_dir.clone(), api_key, github_token);

    // Clone the repository
    let repo_url = "https://github.com/OpenAgentsInc/openagents";
    let _repo = clone_repository(repo_url, &ctx.temp_dir, ctx.github_token.as_deref())?;

    // Checkout the specified branch
    let status = Command::new("git")
        .current_dir(&ctx.temp_dir)
        .args(["checkout", &branch])
        .status()
        .map_err(|e| anyhow::anyhow!("Failed to checkout branch: {}", e))?;

    if !status.success() {
        bail!("Failed to checkout branch: {}", branch);
    }

    // Find the workspace root (where .git directory is)
    let current_dir = std::env::current_dir()?;
    let workspace_root = current_dir
        .ancestors()
        .find(|p| p.join(".git").is_dir())
        .ok_or_else(|| anyhow::anyhow!("Could not find workspace root"))?;

    // Generate the repository map
    let map = generate_repo_map(&ctx.temp_dir);

    // Ensure the docs directory exists
    let docs_dir = workspace_root.join("docs");
    fs::create_dir_all(&docs_dir)?;

    // Write the map to docs/repomap.md in the workspace root
    let repomap_path = docs_dir.join("repomap.md");
    fs::write(&repomap_path, map)?;
    println!("Repository map saved to {}", repomap_path.display());

    // Clean up at the end
    cleanup_temp_dir(&temp_dir);

    // Commit and push the changes
    println!("Committing and pushing changes...");
    Command::new("git")
        .current_dir(workspace_root)
        .args(["add", "docs/repomap.md"])
        .status()?;
    Command::new("git")
        .current_dir(workspace_root)
        .args(["commit", "-m", "Update repomap"])
        .status()?;
    Command::new("git")
        .current_dir(workspace_root)
        .args(["push"])
        .status()?;
    println!("Changes pushed successfully");

    Ok(())
}
