use super::types::SolverStage;
use serde_json::Value;

pub(crate) fn render_progress_bar(stage: &SolverStage) -> String {
    let (percent, stage_text) = match stage {
        SolverStage::Init => (0, "Initializing"),
        SolverStage::Repomap => (25, "Mapping Repository"),
        SolverStage::Analysis => (50, "Analyzing Files"),
        SolverStage::Solution => (75, "Generating Solution"),
        SolverStage::PR => (90, "Preparing Changes"),
    };

    format!(
        r#"<div class="progress-bar">
            <div style="width: {}%" class="transition-all duration-300 ease-in-out">
                <span class="text-xs text-white/75">{}</span>
            </div>
        </div>"#,
        percent,
        stage_text
    )
}

pub(crate) fn render_files_list(files_list: &Value) -> String {
    let files = match files_list {
        Value::Array(files) => files
            .iter()
            .map(|f| f.as_str().unwrap_or_default())
            .collect::<Vec<_>>()
            .join("\n"),
        Value::String(s) => s.clone(),
        _ => files_list.to_string(),
    };

    format!(
        r#"<div class="text-sm space-y-1">
            <div class="text-gray-400 font-medium">Relevant Files:</div>
            <pre class="text-xs text-gray-300 whitespace-pre-wrap">{}</pre>
        </div>"#,
        html_escape::encode_text(&files)
    )
}

pub(crate) fn render_files_reasoning(reasoning: &Value) -> String {
    let text = match reasoning {
        Value::String(s) => s.clone(),
        _ => reasoning.to_string(),
    };

    format!(
        r#"<div class="reasoning-chunk bg-gray-800/50 rounded p-2 mb-2 text-xs text-gray-300 whitespace-pre-wrap">{}</div>"#,
        html_escape::encode_text(&text)
    )
}

pub(crate) fn render_solution(solution: &Value) -> String {
    let text = match solution {
        Value::String(s) => s.clone(),
        _ => solution.to_string(),
    };

    format!(
        r#"<div class="bg-gray-800 rounded-lg p-4">
            <div class="text-sm text-yellow-400 mb-2">Proposed Solution:</div>
            <pre class="text-xs text-gray-300 whitespace-pre-wrap overflow-x-auto">{}</pre>
        </div>"#,
        html_escape::encode_text(&text)
    )
}

pub(crate) fn render_solution_reasoning(reasoning: &Value) -> String {
    let text = match reasoning {
        Value::String(s) => s.clone(),
        _ => reasoning.to_string(),
    };

    format!(
        r#"<div class="reasoning-chunk bg-gray-800/50 rounded p-2 mb-2 text-xs text-gray-300 whitespace-pre-wrap">{}</div>"#,
        html_escape::encode_text(&text)
    )
}

pub(crate) fn render_complete(result: &Value) -> String {
    let solution = match result {
        Value::Object(obj) => obj.get("solution").and_then(|s| s.as_str()).unwrap_or(""),
        Value::String(s) => s.as_str(),
        _ => "",
    };

    format!(
        r#"<div class="bg-gray-800 rounded-lg p-4">
            <div class="text-sm text-green-400 mb-2">Solution Complete</div>
            <pre class="text-xs text-gray-300 whitespace-pre-wrap overflow-x-auto">{}</pre>
        </div>"#,
        html_escape::encode_text(solution)
    )
}

pub(crate) fn render_error(message: &str, details: &Option<String>) -> String {
    format!(
        r#"<div class="bg-red-900/20 border border-red-500/20 rounded p-4">
            <div class="text-sm text-red-400">Error: {}</div>
            {}</div>"#,
        html_escape::encode_text(message),
        details
            .as_ref()
            .map(|d| format!(
                r#"<pre class="mt-2 text-xs text-red-300 whitespace-pre-wrap">{}</pre>"#,
                html_escape::encode_text(d)
            ))
            .unwrap_or_default()
    )
}

// Helper function to format code blocks
fn format_code_block(code: &str, language: Option<&str>) -> String {
    format!(
        r#"<pre class="bg-black/50 rounded p-2 text-xs overflow-x-auto"><code class="language-{}">{}</code></pre>"#,
        language.unwrap_or("plaintext"),
        html_escape::encode_text(code)
    )
}

// Helper function to format markdown-style text
fn format_markdown(text: &str) -> String {
    // Basic markdown formatting - expand as needed
    text.lines()
        .map(|line| {
            if line.starts_with("# ") {
                format!("<h1 class=\"text-lg font-bold\">{}</h1>", &line[2..])
            } else if line.starts_with("## ") {
                format!("<h2 class=\"text-md font-bold\">{}</h2>", &line[3..])
            } else if line.starts_with("- ") {
                format!("<li class=\"ml-4\">{}</li>", &line[2..])
            } else {
                format!("<p>{}</p>", line)
            }
        })
        .collect::<Vec<_>>()
        .join("\n")
}