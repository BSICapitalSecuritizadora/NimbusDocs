<?php
/**
 * Sidebar Premium - Design Financeiro
 */

$currentUri = $_SERVER['REQUEST_URI'] ?? '/admin';
$currentUri = strtok($currentUri, '?'); // Remove query string

// Helper para verificar item ativo
function isActive($path, $current) {
    if ($path === '/admin' || $path === '/admin/dashboard') {
        return $current === '/admin' || $current === '/admin/dashboard';
    }
    return str_starts_with($current, $path);
}

$branding = $branding ?? ($config['branding'] ?? []);
$appName = $branding['app_name'] ?? 'NimbusDocs';
?>

<aside class="nd-sidebar">
    <!-- Brand -->
    <div class="nd-sidebar-brand">
        <div class="nd-sidebar-brand-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <span class="nd-sidebar-brand-text"><?= htmlspecialchars($appName) ?></span>
    </div>
    
    <!-- Navigation -->
    <nav class="nd-sidebar-nav">
        <!-- Principal -->
        <div class="nd-nav-section">
            <div class="nd-nav-section-title">Principal</div>
            
            <a href="/admin/dashboard" class="nd-nav-item <?= isActive('/admin/dashboard', $currentUri) || $currentUri === '/admin' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill nd-nav-icon"></i>
                Dashboard
            </a>
            
            <a href="/admin/submissions" class="nd-nav-item <?= isActive('/admin/submissions', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-inbox-fill nd-nav-icon"></i>
                Envios
            </a>
        </div>
        
        <!-- Gestão -->
        <div class="nd-nav-section">
            <div class="nd-nav-section-title">Gestão</div>
            
            <a href="/admin/users" class="nd-nav-item <?= isActive('/admin/users', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-person-gear nd-nav-icon"></i>
                Administradores
            </a>

            <a href="/admin/portal-users" class="nd-nav-item <?= isActive('/admin/portal-users', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-people-fill nd-nav-icon"></i>
                Usuários do Portal
            </a>

            <a href="/admin/tokens" class="nd-nav-item <?= isActive('/admin/tokens', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-key-fill nd-nav-icon"></i>
                Tokens de Acesso
            </a>
        </div>
        
        <!-- Documentos -->
        <div class="nd-nav-section">
            <div class="nd-nav-section-title">Documentos</div>

            <a href="/admin/document-categories" class="nd-nav-item <?= isActive('/admin/document-categories', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-tags-fill nd-nav-icon"></i>
                Categorias
            </a>

            <a href="/admin/general-documents" class="nd-nav-item <?= isActive('/admin/general-documents', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-folder-fill nd-nav-icon"></i>
                Documentos Gerais
            </a>

            <a href="/admin/documents" class="nd-nav-item <?= isActive('/admin/documents', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-folder-fill nd-nav-icon"></i>
                Documentos para o Usuário
            </a>
        </div>
        
        <!-- Comunicação -->
        <div class="nd-nav-section">
            <div class="nd-nav-section-title">Comunicação</div>
            
            <a href="/admin/announcements" class="nd-nav-item <?= isActive('/admin/announcements', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-megaphone-fill nd-nav-icon"></i>
                Comunicados
            </a>
            
            <a href="/admin/notifications/outbox" class="nd-nav-item <?= isActive('/admin/notifications', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-envelope-paper-fill nd-nav-icon"></i>
                Fila de E-mails
            </a>
        </div>
        
        <!-- Auditoria & Relatórios -->
        <div class="nd-nav-section">
            <div class="nd-nav-section-title">Auditoria</div>
            
            <a href="/admin/audit" class="nd-nav-item <?= isActive('/admin/audit', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-clipboard-check-fill nd-nav-icon"></i>
                Logs de Auditoria
            </a>
            
            <a href="/admin/access-log/portal" class="nd-nav-item <?= isActive('/admin/access-log', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-shield-fill-check nd-nav-icon"></i>
                Log de Acessos
            </a>
            
            <a href="/admin/monitoring" class="nd-nav-item <?= isActive('/admin/monitoring', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-activity nd-nav-icon"></i>
                Monitoramento
            </a>
            
            <a href="/admin/reports/submissions" class="nd-nav-item <?= isActive('/admin/reports', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-graph-up-arrow nd-nav-icon"></i>
                Relatórios
            </a>
        </div>
        
        <!-- Configurações -->
        <div class="nd-nav-section">
            <div class="nd-nav-section-title">Sistema</div>
            
            <a href="/admin/settings/branding" class="nd-nav-item <?= isActive('/admin/settings', $currentUri) ? 'active' : '' ?>">
                <i class="bi bi-gear-fill nd-nav-icon"></i>
                Configurações
            </a>
            
            <a href="/admin/logout" class="nd-nav-item">
                <i class="bi bi-box-arrow-left nd-nav-icon"></i>
                Sair
            </a>
        </div>
    </nav>
</aside>