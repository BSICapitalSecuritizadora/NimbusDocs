-- Migration: Add CORRECTION_FILE to document_type enum
-- Permite que usuários enviem arquivos de correção pela tela de detalhes

ALTER TABLE portal_submission_files 
MODIFY COLUMN document_type ENUM(
    'BALANCE_SHEET',
    'DRE',
    'POLICIES',
    'CNPJ_CARD',
    'POWER_OF_ATTORNEY',
    'MINUTES',
    'ARTICLES_OF_INCORPORATION',
    'BYLAWS',
    'OTHER',
    'CORRECTION_FILE'
) DEFAULT 'OTHER';
