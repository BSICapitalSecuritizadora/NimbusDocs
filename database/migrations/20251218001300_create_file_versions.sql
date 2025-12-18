-- Migration: Create file versions table for document versioning
CREATE TABLE IF NOT EXISTS portal_submission_file_versions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_id INT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL DEFAULT 1,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    storage_path VARCHAR(255) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) DEFAULT NULL,
    checksum VARCHAR(128) DEFAULT NULL,
    uploaded_by_type ENUM('ADMIN','PORTAL_USER') NOT NULL,
    uploaded_by_id INT UNSIGNED NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES portal_submission_files(id) ON DELETE CASCADE,
    KEY idx_file_versions_file (file_id),
    KEY idx_file_versions_version (file_id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add current_version column to files table
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'portal_submission_files' AND COLUMN_NAME = 'current_version');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE portal_submission_files ADD COLUMN current_version INT UNSIGNED NOT NULL DEFAULT 1 AFTER checksum', 
    'SELECT "Column current_version already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
