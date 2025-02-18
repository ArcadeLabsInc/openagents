FROM lukemathwalker/cargo-chef:latest-rust-1.81.0 as chef
WORKDIR /app/backend
RUN apt update && apt install lld clang -y

FROM chef as planner
COPY backend/ .
# Compute a lock-like file for our project
RUN cargo chef prepare --recipe-path recipe.json

FROM chef as builder
WORKDIR /app/backend
COPY --from=planner /app/backend/recipe.json recipe.json
# Build our project dependencies, not our application!
RUN cargo chef cook --release --recipe-path recipe.json
COPY backend/ .
ENV SQLX_OFFLINE true
# Build our project
RUN cargo build --release --bin openagents

# Frontend build
FROM node:20-alpine AS frontend-builder
WORKDIR /app/frontend
COPY frontend/ ./
RUN npm install -g pnpm
RUN pnpm install
RUN pnpm run build

# Frontend runtime
FROM node:20-alpine AS frontend-runtime
WORKDIR /app
COPY --from=frontend-builder /app/frontend/build ./build
COPY --from=frontend-builder /app/frontend/public ./public
COPY frontend/package.json frontend/pnpm-lock.yaml frontend/server.js ./
RUN npm install -g pnpm
RUN pnpm install --prod
ENV NODE_ENV=production
EXPOSE 3000

# Backend runtime
FROM debian:bookworm-slim AS backend-runtime
WORKDIR /app
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends openssl ca-certificates \
    # Clean up
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*
COPY --from=builder /app/backend/target/release/openagents openagents
COPY --from=builder /app/backend/assets assets
COPY backend/configuration configuration
ENV APP_ENVIRONMENT production
EXPOSE 8000

# Final stage with both services
FROM debian:bookworm-slim
WORKDIR /app
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends openssl ca-certificates curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g pnpm \
    # Clean up
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

# Copy backend
COPY --from=backend-runtime /app/openagents ./
COPY --from=backend-runtime /app/assets ./assets
COPY --from=backend-runtime /app/configuration ./configuration

# Copy frontend
COPY --from=frontend-runtime /app/build ./frontend/build
COPY --from=frontend-runtime /app/public ./frontend/public
COPY --from=frontend-runtime /app/package.json ./frontend/
COPY --from=frontend-runtime /app/pnpm-lock.yaml ./frontend/
COPY --from=frontend-runtime /app/server.js ./frontend/

# Copy start script
COPY scripts/start-services.sh ./
RUN chmod +x start-services.sh

EXPOSE 3000 8000
CMD ["./start-services.sh"]