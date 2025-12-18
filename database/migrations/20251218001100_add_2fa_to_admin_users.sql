-- Migration: Add 2FA columns to admin_users table
SET @dbname = DATABASE();
SET @tablename = 'admin_users';

-- Add two_factor_secret column
SET @col_exists_secret = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'two_factor_secret');
SET @sql_secret = IF(@col_exists_secret = 0, 
    'ALTER TABLE admin_users ADD COLUMN two_factor_secret VARCHAR(64) DEFAULT NULL AFTER password_hash', 
    'SELECT "Column two_factor_secret already exists"');
PREPARE stmt FROM @sql_secret; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add two_factor_enabled column
SET @col_exists_enabled = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'two_factor_enabled');
SET @sql_enabled = IF(@col_exists_enabled = 0, 
    'ALTER TABLE admin_users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER two_factor_secret', 
    'SELECT "Column two_factor_enabled already exists"');
PREPARE stmt FROM @sql_enabled; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add two_factor_confirmed_at column
SET @col_exists_confirmed = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'two_factor_confirmed_at');
SET @sql_confirmed = IF(@col_exists_confirmed = 0, 
    'ALTER TABLE admin_users ADD COLUMN two_factor_confirmed_at DATETIME DEFAULT NULL AFTER two_factor_enabled', 
    'SELECT "Column two_factor_confirmed_at already exists"');
PREPARE stmt FROM @sql_confirmed; EXECUTE stmt; DEALLOCATE PREPARE stmt;
