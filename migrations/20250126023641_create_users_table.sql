CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    scramble_id VARCHAR(255) UNIQUE,  -- Optional pseudonymous ID from Scramble OIDC
    github_id BIGINT UNIQUE,          -- Optional GitHub user ID
    github_token TEXT,                -- Optional GitHub access token
    last_login_at TIMESTAMP WITH TIME ZONE,
    metadata JSONB DEFAULT '{}'::jsonb,  -- Flexible storage for user attributes
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Add indexes for faster lookups
CREATE INDEX idx_users_scramble_id ON users(scramble_id);
CREATE INDEX idx_users_github_id ON users(github_id);

-- Add updated_at trigger
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
