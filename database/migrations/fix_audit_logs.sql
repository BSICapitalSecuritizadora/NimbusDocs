-- Adiciona colunas target_type e target_id caso não existam
-- Execute este script diretamente no banco de dados

ALTER TABLE audit_logs 
ADD COLUMN IF NOT EXISTS target_type VARCHAR(100) DEFAULT NULL AFTER action;

ALTER TABLE audit_logs 
ADD COLUMN IF NOT EXISTS target_id INT UNSIGNED DEFAULT NULL AFTER target_type;
