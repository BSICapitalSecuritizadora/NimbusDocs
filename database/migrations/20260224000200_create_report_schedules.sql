-- Migration: Create report_schedules table
-- Permite o agendamento de relatórios assíncronos

CREATE TABLE IF NOT EXISTS report_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_type VARCHAR(64) NOT NULL,
    frequency ENUM('DAILY', 'WEEKLY', 'MONTHLY') NOT NULL DEFAULT 'WEEKLY',
    recipient_emails JSON NOT NULL,
    last_run_at DATETIME NULL DEFAULT NULL,
    next_run_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_next_run (next_run_at, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
