-- Migration: Workflow de Aprovação/Reenvio
-- Adiciona comentários em submissões e status NEEDS_CORRECTION

-- Passo 1: Atualizar ENUM incluindo COMPLETED (para migração segura)
ALTER TABLE portal_submissions 
MODIFY COLUMN status ENUM(
    'PENDING', 
    'UNDER_REVIEW', 
    'NEEDS_CORRECTION', 
    'APPROVED', 
    'REJECTED', 
    'CANCELLED', 
    'COMPLETED'
) NOT NULL DEFAULT 'PENDING';

-- Passo 2: Conversão de dados (COMPLETED -> APPROVED)
UPDATE portal_submissions SET status = 'APPROVED' WHERE status = 'COMPLETED';

-- Passo 3: Definir ENUM final e incluir COMPLETED se quiser manter historico ou remover
-- Optei por remover COMPLETED do ENUM final para forçar uso de APPROVED, já que convertemos os dados acima
ALTER TABLE portal_submissions 
MODIFY COLUMN status ENUM(
    'PENDING', 
    'UNDER_REVIEW', 
    'NEEDS_CORRECTION', 
    'APPROVED', 
    'REJECTED', 
    'CANCELLED'
) NOT NULL DEFAULT 'PENDING';

-- Tabela de comentários em submissões
CREATE TABLE IF NOT EXISTS submission_comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    submission_id INT UNSIGNED NOT NULL,
    author_type ENUM('ADMIN', 'PORTAL_USER') NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    is_internal BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Se true, visível apenas para admins',
    requires_action BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Se true, indica que requer ação do usuário',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_submission (submission_id),
    INDEX idx_author (author_type, author_id),
    FOREIGN KEY (submission_id) REFERENCES portal_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Histórico de mudanças de status
CREATE TABLE IF NOT EXISTS submission_status_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    submission_id INT UNSIGNED NOT NULL,
    old_status VARCHAR(30) NULL,
    new_status VARCHAR(30) NOT NULL,
    changed_by_type ENUM('ADMIN', 'PORTAL_USER', 'SYSTEM') NOT NULL,
    changed_by_id INT UNSIGNED NULL,
    reason TEXT NULL COMMENT 'Motivo da mudança',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_submission (submission_id),
    FOREIGN KEY (submission_id) REFERENCES portal_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar colunas de reenvio em portal_submissions
ALTER TABLE portal_submissions 
ADD COLUMN correction_deadline DATETIME NULL COMMENT 'Prazo para correção',
ADD COLUMN correction_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Número de vezes que foi solicitada correção',
ADD COLUMN last_correction_request_at DATETIME NULL;
