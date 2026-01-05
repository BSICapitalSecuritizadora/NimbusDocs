-- Migration: Create portal_announcements table
-- Created: 2025-12-03
-- Description: Tabela para gerenciar comunicados/avisos exibidos no portal do usuário

CREATE TABLE IF NOT EXISTS portal_announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    level ENUM('info', 'success', 'warning', 'danger') NOT NULL DEFAULT 'info',
    starts_at DATETIME DEFAULT NULL COMMENT 'Data/hora de início da exibição (NULL = imediato)',
    ends_at DATETIME DEFAULT NULL COMMENT 'Data/hora de fim da exibição (NULL = sem fim)',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = inativo',
    created_by_admin INT UNSIGNED NOT NULL COMMENT 'ID do admin que criou',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by_admin) REFERENCES admin_users(id) ON DELETE RESTRICT,
    KEY idx_active_dates (is_active, starts_at, ends_at),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Comunicados e avisos para usuários do portal';
