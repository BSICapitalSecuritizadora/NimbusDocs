-- Tabela de configurações do sistema
CREATE TABLE IF NOT EXISTS app_settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir configurações padrão de notificações
INSERT INTO app_settings (`key`, `value`) VALUES
('notifications.general_documents.enabled', '1'),
('notifications.announcements.enabled', '1'),
('notifications.submission_received.enabled', '1'),
('notifications.submission_status_changed.enabled', '1'),
('notifications.token_created.enabled', '1'),
('notifications.token_expired.enabled', '1'),
('notifications.user_precreated.enabled', '1')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
