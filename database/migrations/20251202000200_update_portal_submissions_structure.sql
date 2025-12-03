-- Adiciona novos campos à tabela portal_submissions
ALTER TABLE portal_submissions
    ADD COLUMN responsible_name VARCHAR(190) DEFAULT NULL AFTER message,
    ADD COLUMN company_cnpj VARCHAR(18) DEFAULT NULL AFTER responsible_name,
    ADD COLUMN company_name VARCHAR(190) DEFAULT NULL AFTER company_cnpj,
    ADD COLUMN main_activity VARCHAR(255) DEFAULT NULL AFTER company_name,
    ADD COLUMN phone VARCHAR(50) DEFAULT NULL AFTER main_activity,
    ADD COLUMN website VARCHAR(255) DEFAULT NULL AFTER phone,
    ADD COLUMN net_worth DECIMAL(15,2) DEFAULT NULL AFTER website,
    ADD COLUMN annual_revenue DECIMAL(15,2) DEFAULT NULL AFTER net_worth,
    ADD COLUMN is_us_person TINYINT(1) DEFAULT 0 AFTER annual_revenue,
    ADD COLUMN is_pep TINYINT(1) DEFAULT 0 AFTER is_us_person,
    ADD COLUMN shareholder_data JSON DEFAULT NULL AFTER is_pep,
    ADD COLUMN registrant_name VARCHAR(190) DEFAULT NULL AFTER shareholder_data,
    ADD COLUMN registrant_position VARCHAR(100) DEFAULT NULL AFTER registrant_name,
    ADD COLUMN registrant_rg VARCHAR(20) DEFAULT NULL AFTER registrant_position,
    ADD COLUMN registrant_cpf VARCHAR(14) DEFAULT NULL AFTER registrant_rg;

-- Cria tabela para composição societária (alternativa ao JSON)
CREATE TABLE IF NOT EXISTS portal_submission_shareholders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    submission_id INT UNSIGNED NOT NULL,
    name VARCHAR(190) NOT NULL,
    document_rg VARCHAR(20) DEFAULT NULL,
    document_cnpj VARCHAR(18) DEFAULT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES portal_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Renomeia tabela de arquivos para manter compatibilidade
-- ou adiciona campo category para diferenciar tipos de documentos
ALTER TABLE portal_submission_files
    ADD COLUMN document_type ENUM(
        'BALANCE_SHEET',
        'DRE',
        'POLICIES',
        'CNPJ_CARD',
        'POWER_OF_ATTORNEY',
        'MINUTES',
        'ARTICLES_OF_INCORPORATION',
        'BYLAWS',
        'OTHER'
    ) DEFAULT 'OTHER' AFTER submission_id;
