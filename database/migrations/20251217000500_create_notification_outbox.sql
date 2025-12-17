CREATE TABLE IF NOT EXISTS notification_outbox (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(100) NOT NULL,
    recipient_email VARCHAR(190) NOT NULL,
    recipient_name VARCHAR(190) DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    template VARCHAR(150) NOT NULL,
    payload_json JSON NOT NULL,
    status ENUM('PENDING','SENDING','SENT','FAILED') NOT NULL DEFAULT 'PENDING',
    attempts INT UNSIGNED NOT NULL DEFAULT 0,
    max_attempts INT UNSIGNED NOT NULL DEFAULT 5,
    next_attempt_at DATETIME DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    last_error TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_notification_outbox_status (status),
    KEY idx_notification_outbox_next_attempt (next_attempt_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;