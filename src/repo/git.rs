use anyhow::{anyhow, Result};
use git2::{Repository, Signature, BranchType, FetchOptions};
use std::fs;
use std::path::PathBuf;
use tracing::debug;

pub fn cleanup_temp_dir(temp_dir: &PathBuf) {
    if temp_dir.exists() {
        if let Err(e) = fs::remove_dir_all(temp_dir) {
            eprintln!("Warning: Failed to clean up temporary directory: {}", e);
        } else {
            println!("Temporary directory removed.");
        }
    }
}

pub fn clone_repository(url: &str, temp_dir: &PathBuf) -> Result<Repository> {
    println!("Cloning repository: {}", url);
    let repo = Repository::clone(url, temp_dir)
        .map_err(|e| anyhow!("Failed to clone repository: {}", e))?;
    println!("Repository cloned successfully into: {:?}", temp_dir);
    Ok(repo)
}

pub fn commit_changes(repo: &Repository, files: &[String], message: &str) -> Result<()> {
    debug!("Committing changes to files: {:?}", files);
    
    let mut index = repo.index()?;
    
    // Add all modified files to the index
    for file in files {
        debug!("Adding file to index: {}", file);
        index.add_path(std::path::Path::new(file))?;
    }
    
    index.write()?;
    
    let tree_id = index.write_tree()?;
    let tree = repo.find_tree(tree_id)?;
    
    let head = repo.head()?;
    let parent_commit = repo.find_commit(head.target().ok_or_else(|| anyhow!("No HEAD target"))?)?;
    
    let signature = Signature::now("OpenAgents Solver", "solver@openagents.com")?;
    
    repo.commit(
        Some("HEAD"),
        &signature,
        &signature,
        message,
        &tree,
        &[&parent_commit],
    )?;
    
    debug!("Changes committed successfully");
    Ok(())
}

pub fn checkout_branch(repo: &Repository, branch_name: &str) -> Result<()> {
    debug!("Checking out branch: {}", branch_name);

    // First try to find the local branch
    let branch = match repo.find_branch(branch_name, BranchType::Local) {
        Ok(branch) => branch,
        Err(_) => {
            debug!("Branch not found locally, fetching from remote");
            
            // Get the remote
            let mut remote = repo.find_remote("origin")?;
            
            // Set up fetch options
            let mut fetch_opts = FetchOptions::new();
            
            // Fetch from remote
            debug!("Fetching from remote");
            remote.fetch(&[branch_name], Some(&mut fetch_opts), None)?;
            
            // Create local branch from remote
            let remote_branch = format!("origin/{}", branch_name);
            debug!("Looking up remote branch: {}", remote_branch);
            let commit = repo.revparse_single(&remote_branch)?;
            
            debug!("Creating local branch from remote");
            repo.branch(branch_name, &commit.peel_to_commit()?, false)?;
            
            repo.find_branch(branch_name, BranchType::Local)?
        }
    };

    let reference = branch.get();
    let commit = reference.peel_to_commit()?;
    
    debug!("Setting HEAD to branch");
    repo.set_head(reference.name().ok_or_else(|| anyhow!("Invalid reference name"))?)?;
    
    debug!("Checking out tree");
    repo.checkout_tree(commit.as_object(), None)?;

    debug!("Successfully checked out branch: {}", branch_name);
    Ok(())
}