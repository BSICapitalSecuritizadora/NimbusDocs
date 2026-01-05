<aside id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-dark text-white">
    <a href="/admin/dashboard" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none">
        <span class="fs-5 fw-bold">NimbusDocs</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">

        <li class="nav-item">
            <a href="/admin/dashboard" class="nav-link text-white">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="/admin/submissions" class="nav-link text-white">
                <i class="bi bi-inboxes me-2"></i> Submissões
            </a>
        </li>

        <li>
            <a href="/admin/users" class="nav-link text-white">
                <i class="bi bi-people me-2"></i> Usuários (Admins)
            </a>
        </li>

        <li>
            <a href="/admin/portal-users" class="nav-link text-white">
                <i class="bi bi-people me-2"></i> Usuários (Portal)
            </a>
        </li>

        <li>
            <a href="/admin/tokens" class="nav-link text-white">
                <i class="bi bi-key me-2"></i> Tokens de Acesso
            </a>
        </li>

        <li>
            <a href="/admin/audit" class="nav-link text-white">
                <i class="bi bi-clipboard-check me-2"></i> Auditoria
            </a>
        </li>

        <li class="nav-item">
            <a href="/admin/access-log/portal" class="nav-link text-white">
                <i class="bi bi-shield-check me-2"></i> Log de acessos (portal)
            </a>
        </li>

        <li>
            <a href="/admin/settings/notifications" class="nav-link text-white">
                <i class="bi bi-gear me-2"></i> Configurações
            </a>
        </li>

        <li class="nav-item">
            <a href="/admin/settings/notifications" class="nav-link text-white">
                <i class="bi bi-bell me-2"></i> Notificações
            </a>
        </li>

        <li class="nav-item">
            <a href="/admin/settings/branding" class="nav-link text-white">
                <i class="bi bi-palette me-2"></i> Branding
            </a>
        </li>

        <li class="nav-item">
            <a href="/admin/announcements" class="nav-link text-white">
                <i class="bi bi-megaphone me-2"></i> Comunicados
            </a>
        </li>

        <li class="nav-item">
            <a href="/admin/notifications/outbox" class="nav-link text-white">
                <i class="bi bi-envelope-paper me-2"></i> Fila de notificações
            </a>
        </li>

        <li class="nav-item mt-3">
            <span class="nav-link text-uppercase text-white-50 small">
                Relatórios
            </span>
        </li>
        <li class="nav-item">
            <a href="/admin/reports/submissions" class="nav-link text-white">
                <i class="bi bi-graph-up-arrow me-2"></i> Submissões
            </a>
        </li>

        <li class="nav-item">
            <a href="/admin/general-documents" class="nav-link text-white">
                <i class="bi bi-folder2-open me-2"></i> Documentos gerais
            </a>
        </li>
        <li class="nav-item">
            <a href="/admin/document-categories" class="nav-link text-white">
                <i class="bi bi-tags me-2"></i> Categorias de documentos
            </a>
        </li>

    </ul>
    <hr>
    <div>
        <a href="/admin/logout" class="nav-link text-white">
            <i class="bi bi-box-arrow-right me-2"></i> Sair
        </a>
    </div>
</aside>

<style>
    .sidebar {
        width: 240px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }
    }
</style>