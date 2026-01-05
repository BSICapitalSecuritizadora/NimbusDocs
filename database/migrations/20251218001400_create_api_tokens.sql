-- Migration: Create API tokens table for REST API authentication
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    last_4 VARCHAR(4) NOT NULL,
    scopes JSON DEFAULT NULL,
    last_used_at DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    revoked_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    KEY idx_api_tokens_hash (token_hash),
    KEY idx_api_tokens_user (admin_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
