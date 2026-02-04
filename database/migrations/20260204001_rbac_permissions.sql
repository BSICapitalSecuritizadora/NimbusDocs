-- Migration: RBAC Granular Permissions
-- Sistema de permissões por recurso com roles ADMIN, AUDITOR, OPERATOR

-- Adicionar coluna role se não existir (ou atualizar ENUM)
ALTER TABLE admin_users 
MODIFY COLUMN role ENUM('SUPER_ADMIN', 'ADMIN', 'OPERATOR', 'AUDITOR') NOT NULL DEFAULT 'OPERATOR';

-- Tabela de permissões (recursos e ações disponíveis)
CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource VARCHAR(50) NOT NULL COMMENT 'Ex: submissions, users, reports',
    action VARCHAR(30) NOT NULL COMMENT 'Ex: view, create, edit, delete, export',
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_resource_action (resource, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de permissões por role
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role ENUM('SUPER_ADMIN', 'ADMIN', 'OPERATOR', 'AUDITOR') NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_role_permission (role, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert permissões base
INSERT INTO permissions (resource, action, description) VALUES
-- Submissions
('submissions', 'view', 'Ver submissões'),
('submissions', 'create', 'Criar submissões'),
('submissions', 'edit', 'Editar submissões'),
('submissions', 'delete', 'Excluir submissões'),
('submissions', 'export', 'Exportar submissões'),
('submissions', 'review', 'Aprovar/reprovar submissões'),
-- Users
('portal_users', 'view', 'Ver usuários do portal'),
('portal_users', 'create', 'Criar usuários do portal'),
('portal_users', 'edit', 'Editar usuários do portal'),
('portal_users', 'delete', 'Excluir usuários do portal'),
-- Admin Users
('admin_users', 'view', 'Ver administradores'),
('admin_users', 'create', 'Criar administradores'),
('admin_users', 'edit', 'Editar administradores'),
('admin_users', 'delete', 'Excluir administradores'),
-- Reports
('reports', 'view', 'Ver relatórios'),
('reports', 'export', 'Exportar relatórios'),
-- Audit
('audit_logs', 'view', 'Ver logs de auditoria'),
('audit_logs', 'export', 'Exportar logs de auditoria'),
-- Documents
('documents', 'view', 'Ver documentos'),
('documents', 'create', 'Criar documentos'),
('documents', 'edit', 'Editar documentos'),
('documents', 'delete', 'Excluir documentos'),
-- Settings
('settings', 'view', 'Ver configurações'),
('settings', 'edit', 'Alterar configurações'),
-- Monitoring
('monitoring', 'view', 'Ver monitoramento'),
-- Notifications
('notifications', 'view', 'Ver notificações'),
('notifications', 'manage', 'Gerenciar notificações');

-- SUPER_ADMIN tem tudo
INSERT INTO role_permissions (role, permission_id)
SELECT 'SUPER_ADMIN', id FROM permissions;

-- ADMIN tem quase tudo exceto admin_users.delete e settings.edit crítico
INSERT INTO role_permissions (role, permission_id)
SELECT 'ADMIN', id FROM permissions 
WHERE NOT (resource = 'admin_users' AND action = 'delete');

-- OPERATOR pode ver e editar submissions/documents, não pode settings
INSERT INTO role_permissions (role, permission_id)
SELECT 'OPERATOR', id FROM permissions 
WHERE resource IN ('submissions', 'documents', 'portal_users', 'notifications')
  AND action IN ('view', 'create', 'edit', 'review');

-- AUDITOR só leitura e exports
INSERT INTO role_permissions (role, permission_id)
SELECT 'AUDITOR', id FROM permissions 
WHERE action IN ('view', 'export');
