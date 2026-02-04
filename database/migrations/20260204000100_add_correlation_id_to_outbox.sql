-- Migration: Add correlation_id to notification_outbox
-- Permite rastrear a cadeia submissão → notificação → falha

ALTER TABLE notification_outbox 
ADD COLUMN correlation_id VARCHAR(64) NULL AFTER payload_json,
ADD INDEX idx_correlation_id (correlation_id);
