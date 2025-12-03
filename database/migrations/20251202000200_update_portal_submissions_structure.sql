-- Adiciona novos campos à tabela portal_submissions (com verificação)
SET @dbname = DATABASE();
SET @tablename = 'portal_submissions';

SET @col_exists_responsible_name = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'responsible_name');
SET @col_exists_company_cnpj = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'company_cnpj');
SET @col_exists_company_name = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'company_name');
SET @col_exists_main_activity = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'main_activity');
SET @col_exists_phone = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'phone');
SET @col_exists_website = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'website');
SET @col_exists_net_worth = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'net_worth');
SET @col_exists_annual_revenue = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'annual_revenue');
SET @col_exists_is_us_person = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'is_us_person');
SET @col_exists_is_pep = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'is_pep');
SET @col_exists_shareholder_data = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shareholder_data');
SET @col_exists_registrant_name = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'registrant_name');
SET @col_exists_registrant_position = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'registrant_position');
SET @col_exists_registrant_rg = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'registrant_rg');
SET @col_exists_registrant_cpf = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'registrant_cpf');

SET @sql_responsible_name = IF(@col_exists_responsible_name = 0, 'ALTER TABLE portal_submissions ADD COLUMN responsible_name VARCHAR(190) DEFAULT NULL AFTER message', 'SELECT "Column responsible_name already exists"');
SET @sql_company_cnpj = IF(@col_exists_company_cnpj = 0, 'ALTER TABLE portal_submissions ADD COLUMN company_cnpj VARCHAR(18) DEFAULT NULL AFTER responsible_name', 'SELECT "Column company_cnpj already exists"');
SET @sql_company_name = IF(@col_exists_company_name = 0, 'ALTER TABLE portal_submissions ADD COLUMN company_name VARCHAR(190) DEFAULT NULL AFTER company_cnpj', 'SELECT "Column company_name already exists"');
SET @sql_main_activity = IF(@col_exists_main_activity = 0, 'ALTER TABLE portal_submissions ADD COLUMN main_activity VARCHAR(255) DEFAULT NULL AFTER company_name', 'SELECT "Column main_activity already exists"');
SET @sql_phone = IF(@col_exists_phone = 0, 'ALTER TABLE portal_submissions ADD COLUMN phone VARCHAR(50) DEFAULT NULL AFTER main_activity', 'SELECT "Column phone already exists"');
SET @sql_website = IF(@col_exists_website = 0, 'ALTER TABLE portal_submissions ADD COLUMN website VARCHAR(255) DEFAULT NULL AFTER phone', 'SELECT "Column website already exists"');
SET @sql_net_worth = IF(@col_exists_net_worth = 0, 'ALTER TABLE portal_submissions ADD COLUMN net_worth DECIMAL(15,2) DEFAULT NULL AFTER website', 'SELECT "Column net_worth already exists"');
SET @sql_annual_revenue = IF(@col_exists_annual_revenue = 0, 'ALTER TABLE portal_submissions ADD COLUMN annual_revenue DECIMAL(15,2) DEFAULT NULL AFTER net_worth', 'SELECT "Column annual_revenue already exists"');
SET @sql_is_us_person = IF(@col_exists_is_us_person = 0, 'ALTER TABLE portal_submissions ADD COLUMN is_us_person TINYINT(1) DEFAULT 0 AFTER annual_revenue', 'SELECT "Column is_us_person already exists"');
SET @sql_is_pep = IF(@col_exists_is_pep = 0, 'ALTER TABLE portal_submissions ADD COLUMN is_pep TINYINT(1) DEFAULT 0 AFTER is_us_person', 'SELECT "Column is_pep already exists"');
SET @sql_shareholder_data = IF(@col_exists_shareholder_data = 0, 'ALTER TABLE portal_submissions ADD COLUMN shareholder_data JSON DEFAULT NULL AFTER is_pep', 'SELECT "Column shareholder_data already exists"');
SET @sql_registrant_name = IF(@col_exists_registrant_name = 0, 'ALTER TABLE portal_submissions ADD COLUMN registrant_name VARCHAR(190) DEFAULT NULL AFTER shareholder_data', 'SELECT "Column registrant_name already exists"');
SET @sql_registrant_position = IF(@col_exists_registrant_position = 0, 'ALTER TABLE portal_submissions ADD COLUMN registrant_position VARCHAR(100) DEFAULT NULL AFTER registrant_name', 'SELECT "Column registrant_position already exists"');
SET @sql_registrant_rg = IF(@col_exists_registrant_rg = 0, 'ALTER TABLE portal_submissions ADD COLUMN registrant_rg VARCHAR(20) DEFAULT NULL AFTER registrant_position', 'SELECT "Column registrant_rg already exists"');
SET @sql_registrant_cpf = IF(@col_exists_registrant_cpf = 0, 'ALTER TABLE portal_submissions ADD COLUMN registrant_cpf VARCHAR(14) DEFAULT NULL AFTER registrant_rg', 'SELECT "Column registrant_cpf already exists"');

PREPARE stmt FROM @sql_responsible_name; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_company_cnpj; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_company_name; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_main_activity; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_phone; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_website; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_net_worth; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_annual_revenue; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_is_us_person; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_is_pep; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_shareholder_data; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_registrant_name; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_registrant_position; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_registrant_rg; EXECUTE stmt; DEALLOCATE PREPARE stmt;
PREPARE stmt FROM @sql_registrant_cpf; EXECUTE stmt; DEALLOCATE PREPARE stmt;

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

-- Adiciona campo document_type se não existir
SET @col_exists_document_type = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'portal_submission_files' AND COLUMN_NAME = 'document_type');
SET @sql_document_type = IF(@col_exists_document_type = 0, 
    'ALTER TABLE portal_submission_files ADD COLUMN document_type ENUM(''BALANCE_SHEET'',''DRE'',''POLICIES'',''CNPJ_CARD'',''POWER_OF_ATTORNEY'',''MINUTES'',''ARTICLES_OF_INCORPORATION'',''BYLAWS'',''OTHER'') DEFAULT ''OTHER'' AFTER submission_id',
    'SELECT "Column document_type already exists"');
PREPARE stmt FROM @sql_document_type; EXECUTE stmt; DEALLOCATE PREPARE stmt;

