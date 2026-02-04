-- Migration: LGPD Compliance
-- Consentimento, retenção de dados, termos de uso

-- Log de consentimentos do usuário
CREATE TABLE IF NOT EXISTS consent_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('PORTAL_USER', 'ADMIN') NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    consent_type VARCHAR(50) NOT NULL COMMENT 'Ex: terms, privacy, marketing, data_processing',
    version VARCHAR(20) NOT NULL COMMENT 'Versão do documento aceito',
    action ENUM('GRANTED', 'REVOKED') NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    metadata JSON NULL COMMENT 'Dados adicionais como hash do documento',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_type, user_id),
    INDEX idx_type_version (consent_type, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Termos e políticas versionados
CREATE TABLE IF NOT EXISTS legal_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('terms_of_use', 'privacy_policy', 'data_processing', 'cookies') NOT NULL,
    version VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Apenas uma versão ativa por tipo',
    requires_acceptance BOOLEAN NOT NULL DEFAULT TRUE,
    published_at DATETIME NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_type_version (type, version),
    INDEX idx_active (type, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Políticas de retenção de dados
CREATE TABLE IF NOT EXISTS data_retention_policies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    data_type VARCHAR(50) NOT NULL COMMENT 'Ex: submissions, audit_logs, access_logs',
    retention_days INT UNSIGNED NOT NULL COMMENT 'Dias para manter os dados',
    action_type ENUM('DELETE', 'ANONYMIZE', 'ARCHIVE') NOT NULL DEFAULT 'DELETE',
    description TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_executed_at DATETIME NULL,
    next_execution_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_data_type (data_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Solicitações de anonimização/exclusão (direito ao esquecimento)
CREATE TABLE IF NOT EXISTS data_subject_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('PORTAL_USER') NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    request_type ENUM('ACCESS', 'RECTIFICATION', 'ERASURE', 'PORTABILITY', 'RESTRICTION') NOT NULL,
    status ENUM('PENDING', 'PROCESSING', 'COMPLETED', 'REJECTED') NOT NULL DEFAULT 'PENDING',
    reason TEXT NULL,
    processed_by INT UNSIGNED NULL,
    processed_at DATETIME NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_type, user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar aceite de termos ao portal_users
ALTER TABLE portal_users 
ADD COLUMN terms_accepted_at DATETIME NULL,
ADD COLUMN terms_version VARCHAR(20) NULL,
ADD COLUMN privacy_accepted_at DATETIME NULL,
ADD COLUMN privacy_version VARCHAR(20) NULL;

-- Insert políticas de retenção padrão
INSERT INTO data_retention_policies (data_type, retention_days, action_type, description) VALUES
('audit_logs', 365, 'ARCHIVE', 'Logs de auditoria: manter 1 ano, depois arquivar'),
('access_logs', 90, 'DELETE', 'Logs de acesso: manter 90 dias'),
('request_logs', 30, 'DELETE', 'Logs de requisições HTTP: manter 30 dias'),
('failed_notifications', 180, 'DELETE', 'Notificações falhas: manter 6 meses'),
('inactive_users', 730, 'ANONYMIZE', 'Usuários inativos há 2 anos: anonimizar');

-- Insert documento de termos inicial
INSERT INTO legal_documents (type, version, title, content, is_active, requires_acceptance) VALUES
('terms_of_use', '1.0', 'Termos de Uso', 
 '# Termos de Uso do NimbusDocs\n\n## 1. Aceitação\nAo utilizar este sistema, você concorda com estes termos.\n\n## 2. Uso Adequado\nO sistema deve ser utilizado apenas para fins profissionais.\n\n## 3. Privacidade\nSeus dados são tratados conforme nossa Política de Privacidade.',
 TRUE, TRUE),
('privacy_policy', '1.0', 'Política de Privacidade',
 '# Política de Privacidade\n\n## 1. Dados Coletados\nColetamos dados necessários para prestação do serviço.\n\n## 2. Uso dos Dados\nSeus dados são usados apenas para os fins descritos.\n\n## 3. Seus Direitos (LGPD)\n- Acesso aos seus dados\n- Correção de dados incorretos\n- Exclusão de dados\n- Portabilidade',
 TRUE, TRUE);
