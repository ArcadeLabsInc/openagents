use openagents::server::config::configure_app;
use tracing::info;
use std::net::SocketAddr;
use hyper::server::conn::AddrIncoming;
use axum::Router;

#[tokio::main]
async fn main() {
    // Initialize logging
    tracing_subscriber::fmt::init();

    // Load environment variables
    dotenvy::dotenv().ok();

    // Create and configure the app
    let app = configure_app();

    // Get port from environment variable or use default
    let port = std::env::var("PORT")
        .ok()
        .and_then(|p| p.parse().ok())
        .unwrap_or(8000);

    let addr = SocketAddr::from(([0, 0, 0, 0], port));
    info!("Starting server on {}", addr);

    // Start the server
    info!("✨ Server ready:");
    info!("  🌎 http://{}", addr);
    
    hyper::Server::bind(&addr)
        .serve(app.into_make_service())
        .await
        .unwrap();
}