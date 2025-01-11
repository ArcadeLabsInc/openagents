use sqlx::postgres::PgPoolOptions;
use sqlx::PgPool;
use tracing::error;
use crate::configuration::Settings;

pub async fn get_connection_pool(configuration: &Settings) -> Result<PgPool, sqlx::Error> {
    error!("Creating database connection pool...");
    
    let connect_options = configuration.database.connect_options();

    error!("Attempting to connect to database...");

    PgPoolOptions::new()
        .max_connections(5)
        .connect_with(connect_options)
        .await
}

pub async fn migrate_database(pool: &PgPool) -> Result<(), sqlx::Error> {
    error!("Running database migrations...");
    sqlx::migrate!("./migrations")
        .run(pool)
        .await
        .map_err(|e| {
            error!("Migration error: {}", e);
            sqlx::Error::Protocol(format!("Migration error: {}", e))
        })
}