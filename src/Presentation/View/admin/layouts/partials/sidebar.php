<?php
/**
 * Sidebar Premium v2.0 - Design Financeiro
 * BSI Capital Securitizadora
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

// Get first word/abbreviation for icon
$appInitials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $appName), 0, 2));
?>

<aside class="nd-sidebar" id="mainSidebar">
    <!-- Brand -->
    <div class="nd-sidebar-brand">
        <div class="nd-sidebar-brand-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <span class="nd-sidebar-brand-text"><?= htmlspecialchars($appName) ?></span>
    </div>
    
    <!-- Navigation -->
    <nav class="nd-sidebar-nav">
        <!-- Visão Geral -->
        <div class="nd-nav-section-title">Visão Geral</div>
        
        <a href="/admin/dashboard" class="nd-nav-item <?= isActive('/admin/dashboard', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill nd-nav-icon"></i>
            <span>Painel de Controle</span>
        </a>
        
        <a href="/admin/submissions" class="nd-nav-item <?= isActive('/admin/submissions', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-inbox-fill nd-nav-icon"></i>
            <span>Envios e Solicitações</span>
        </a>
        
        <!-- Administração -->
        <div class="nd-nav-section-title">Administração</div>
        
        <a href="/admin/users" class="nd-nav-item <?= isActive('/admin/users', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-person-gear nd-nav-icon"></i>
            <span>Administradores</span>
        </a>

        <a href="/admin/portal-users" class="nd-nav-item <?= isActive('/admin/portal-users', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-people-fill nd-nav-icon"></i>
            <span>Usuários do Portal</span>
        </a>

        <a href="/admin/tokens" class="nd-nav-item <?= isActive('/admin/tokens', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-key-fill nd-nav-icon"></i>
            <span>Chaves de Acesso</span>
        </a>
        
        <!-- Gestão Documental -->
        <div class="nd-nav-section-title">Gestão Documental</div>

        <a href="/admin/document-categories" class="nd-nav-item <?= isActive('/admin/document-categories', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-bookmark-fill nd-nav-icon"></i>
            <span>Categorias de Docs</span>
        </a>

        <a href="/admin/general-documents" class="nd-nav-item <?= isActive('/admin/general-documents', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-folder-fill nd-nav-icon"></i>
            <span>Biblioteca Geral</span>
        </a>

        <a href="/admin/documents" class="nd-nav-item <?= isActive('/admin/documents', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-folder2-open nd-nav-icon"></i>
            <span>Docs por Usuário</span>
        </a>
        
        <!-- Comunicação -->
        <div class="nd-nav-section-title">Comunicação</div>
        
        <a href="/admin/announcements" class="nd-nav-item <?= isActive('/admin/announcements', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-megaphone-fill nd-nav-icon"></i>
            <span>Avisos Gerais</span>
        </a>
        
        <a href="/admin/notifications/outbox" class="nd-nav-item <?= isActive('/admin/notifications', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-envelope-paper-fill nd-nav-icon"></i>
            <span>Auditoria de Envios</span>
        </a>

        <a href="/admin/settings/notifications" class="nd-nav-item <?= isActive('/admin/settings/notifications', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-bell-fill nd-nav-icon"></i>
            <span>Config. Notificações</span>
        </a>
        
        <!-- Compliance & Auditoria -->
        <div class="nd-nav-section-title">Compliance</div>
        
        <a href="/admin/access-log/portal" class="nd-nav-item <?= isActive('/admin/access-log', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-shield-fill-check nd-nav-icon"></i>
            <span>Histórico de Acessos</span>
        </a>

        <a href="/admin/audit" class="nd-nav-item <?= isActive('/admin/audit', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-clipboard-check-fill nd-nav-icon"></i>
            <span>Trilha de Auditoria</span>
        </a>
        
        <a href="/admin/monitoring" class="nd-nav-item <?= isActive('/admin/monitoring', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-activity nd-nav-icon"></i>
            <span>Saúde do Sistema</span>
        </a>
        
        <a href="/admin/reports/submissions" class="nd-nav-item <?= isActive('/admin/reports', $currentUri) ? 'active' : '' ?>">
            <i class="bi bi-graph-up-arrow nd-nav-icon"></i>
            <span>Relatórios</span>
        </a>
        
        <!-- Conta -->
        <div class="nd-nav-section-title">Conta</div>
        
        <a href="/admin/settings/branding" class="nd-nav-item <?= isActive('/admin/settings', $currentUri) && !str_contains($currentUri, '/admin/settings/notifications') ? 'active' : '' ?>">
            <i class="bi bi-gear-fill nd-nav-icon"></i>
            <span>Configurações</span>
        </a>
        
        <a href="/admin/logout" class="nd-nav-item nd-nav-item-logout">
            <i class="bi bi-box-arrow-left nd-nav-icon"></i>
            <span>Sair</span>
        </a>
    </nav>
</aside>

<style>
    /* Sidebar logout item */
    .nd-nav-item-logout {
        margin-top: 1rem;
        opacity: 0.7;
    }
    
    .nd-nav-item-logout:hover {
        opacity: 1;
        background: rgba(239, 68, 68, 0.1) !important;
        color: #fca5a5 !important;
    }
    
    /* Mobile toggle button (added via JS) */
    .nd-sidebar-toggle {
        display: none;
        position: fixed;
        bottom: 1.5rem;
        left: 1.5rem;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--nd-gold-500) 0%, var(--nd-gold-600) 100%);
        border: none;
        border-radius: 50%;
        color: var(--nd-navy-900);
        font-size: 1.25rem;
        cursor: pointer;
        z-index: 1001;
        box-shadow: var(--nd-shadow-lg), var(--nd-glow-gold);
        transition: var(--nd-transition);
    }
    
    .nd-sidebar-toggle:hover {
        transform: scale(1.1);
    }
    
    @media (max-width: 991.98px) {
        .nd-sidebar-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nd-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .nd-sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
    }
</style>

<!-- Mobile Sidebar Toggle -->
<button class="nd-sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>
<div class="nd-sidebar-overlay" id="sidebarOverlay"></div>

<script>
(function() {
    const sidebar = document.getElementById('mainSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (toggle && sidebar && overlay) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            toggle.querySelector('i').classList.toggle('bi-list');
            toggle.querySelector('i').classList.toggle('bi-x-lg');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            toggle.querySelector('i').classList.add('bi-list');
            toggle.querySelector('i').classList.remove('bi-x-lg');
        });
    }
})();
</script>