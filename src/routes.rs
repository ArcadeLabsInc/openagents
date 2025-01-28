use askama::Template;
use axum::{
    http::header::{HeaderMap, HeaderValue},
    response::{Html, IntoResponse, Response},
    Json,
};
use serde_json::json;
use std::sync::Arc;

use crate::server::services::RepomapService;

#[derive(Template)]
#[template(path = "layouts/base.html", escape = "none")]
struct PageTemplate<'a> {
    title: &'a str,
    path: &'a str,
}

#[derive(Template)]
#[template(path = "layouts/content.html", escape = "none")]
struct ContentTemplate<'a> {
    path: &'a str,
}

#[derive(Template)]
#[template(path = "layouts/chat_base.html")]
struct ChatPageTemplate<'a> {
    title: &'a str,
}

#[derive(Template)]
#[template(path = "layouts/chat_content.html")]
struct ChatContentTemplate;

#[derive(Template)]
#[template(path = "pages/login.html")]
struct LoginTemplate {
    title: String,
}

#[derive(Template)]
#[template(path = "pages/signup.html")]
struct SignupTemplate {
    title: String,
}

pub async fn health_check() -> Json<serde_json::Value> {
    Json(json!({ "status": "healthy" }))
}

pub async fn home(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Home";
    let path = "/";

    if is_htmx {
        let content = ContentTemplate { path }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = PageTemplate { title, path };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn login(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Log in".to_string();

    if is_htmx {
        let content = LoginTemplate { title }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str("OpenAgents - Log in").unwrap(),
        );
        response
    } else {
        let template = LoginTemplate { title };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn signup(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Sign up".to_string();

    if is_htmx {
        let content = SignupTemplate { title }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str("OpenAgents - Sign up").unwrap(),
        );
        response
    } else {
        let template = SignupTemplate { title };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn chat(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Chat";

    if is_htmx {
        let content = ChatContentTemplate.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = ChatPageTemplate { title };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn mobile_app(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Mobile App";
    let path = "/onyx";

    if is_htmx {
        let content = ContentTemplate { path }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = PageTemplate { title, path };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn business(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Services";
    let path = "/services";

    if is_htmx {
        let content = ContentTemplate { path }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = PageTemplate { title, path };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn video_series(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Video Series";
    let path = "/video-series";

    if is_htmx {
        let content = ContentTemplate { path }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = PageTemplate { title, path };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn company(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Company";
    let path = "/company";

    if is_htmx {
        let content = ContentTemplate { path }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = PageTemplate { title, path };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn coming_soon(headers: HeaderMap) -> Response {
    let is_htmx = headers.contains_key("hx-request");
    let title = "Coming Soon";
    let path = "/coming-soon";

    if is_htmx {
        let content = ContentTemplate { path }.render().unwrap();
        let mut response = Response::new(content.into());
        response.headers_mut().insert(
            "HX-Title",
            HeaderValue::from_str(&format!("OpenAgents - {}", title)).unwrap(),
        );
        response
    } else {
        let template = PageTemplate { title, path };
        Html(template.render().unwrap()).into_response()
    }
}

pub async fn repomap() -> Response {
    let title = "Repository Map";
    let path = "/repomap";
    let template = PageTemplate { title, path };
    Html(template.render().unwrap()).into_response()
}

pub async fn generate_repomap(
    axum::extract::State(service): axum::extract::State<Arc<RepomapService>>,
    axum::Json(body): axum::Json<serde_json::Value>,
) -> Json<serde_json::Value> {
    match service.generate_repomap(body.to_string()).await {
        Ok(result) => Json(json!({ "result": result })),
        Err(e) => Json(json!({ "error": e.to_string() })),
    }
}
