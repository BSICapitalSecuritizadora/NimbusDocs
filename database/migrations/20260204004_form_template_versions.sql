-- Migration: Templates de Formulário Versionados
-- Permite mudanças de campos sem quebrar histórico

-- Tabela de templates de formulário
CREATE TABLE IF NOT EXISTS form_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Código único do template (ex: submission_default)',
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Versões dos templates
CREATE TABLE IF NOT EXISTS form_template_versions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_id INT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL COMMENT 'Número sequencial da versão',
    schema_json JSON NOT NULL COMMENT 'Definição dos campos do formulário',
    is_current BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Versão ativa para novos formulários',
    changelog TEXT NULL COMMENT 'Descrição das mudanças',
    created_by INT UNSIGNED NULL,
    published_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_template_version (template_id, version),
    INDEX idx_current (template_id, is_current),
    FOREIGN KEY (template_id) REFERENCES form_templates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar referência de versão nas submissões
ALTER TABLE portal_submissions 
ADD COLUMN form_template_version_id INT UNSIGNED NULL,
ADD CONSTRAINT fk_submission_template_version 
    FOREIGN KEY (form_template_version_id) REFERENCES form_template_versions(id);

-- Insert template padrão
INSERT INTO form_templates (code, name, description) VALUES
('submission_default', 'Formulário de Submissão Padrão', 'Template padrão para submissões do portal');

-- Insert versão inicial do template
INSERT INTO form_template_versions (template_id, version, schema_json, is_current, changelog) 
SELECT 
    id,
    1,
    JSON_OBJECT(
        'fields', JSON_ARRAY(
            JSON_OBJECT('name', 'title', 'type', 'text', 'label', 'Título', 'required', true),
            JSON_OBJECT('name', 'description', 'type', 'textarea', 'label', 'Descrição', 'required', false),
            JSON_OBJECT('name', 'category', 'type', 'select', 'label', 'Categoria', 'required', true),
            JSON_OBJECT('name', 'files', 'type', 'file', 'label', 'Arquivos', 'required', true, 'multiple', true)
        ),
        'validation', JSON_OBJECT(
            'maxFiles', 10,
            'maxFileSize', 52428800
        )
    ),
    TRUE,
    'Versão inicial do template de submissão'
FROM form_templates 
WHERE code = 'submission_default';
