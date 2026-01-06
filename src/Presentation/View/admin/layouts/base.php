<?php

/** @var string $pageTitle */
/** @var array $viewData */
$branding = $viewData['branding'] ?? ($branding ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']       ?? 'NimbusDocs';
$primary  = $branding['primary_color']  ?? '#0a1628';
$accent   = $branding['accent_color']   ?? '#d4a84b';

// Get admin user for avatar
$admin = $_SESSION['admin'] ?? [];
$adminName = $admin['name'] ?? 'Admin';
$adminInitials = strtoupper(substr($adminName, 0, 2));
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($appName . ' — ' . ($pageTitle ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- NimbusDocs Theme -->
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
</head>

<body>
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="nd-main">
        <!-- Header -->
        <header class="nd-header">
            <div class="nd-header-title">
                <?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?>
            </div>
            
            <div class="nd-header-actions">
                <!-- Search -->
                <form action="/admin/search" method="GET" class="d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" 
                               name="q" 
                               class="nd-input" 
                               placeholder="Buscar..." 
                               style="width: 240px; padding-left: 2.5rem; padding-right: 1rem; height: 38px;">
                        <i class="bi bi-search position-absolute" 
                           style="left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--nd-gray-400);"></i>
                    </div>
                </form>
                
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="nd-btn nd-btn-outline nd-btn-sm position-relative" 
                            type="button" 
                            data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.65rem;" 
                              id="notificationBadge">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <li><h6 class="dropdown-header">Notificações</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item-text text-muted small">Nenhuma notificação</span></li>
                    </ul>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="nd-btn nd-btn-outline nd-btn-sm d-flex align-items-center gap-2" 
                            type="button" 
                            data-bs-toggle="dropdown">
                        <div class="nd-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">
                            <?= htmlspecialchars($adminInitials) ?>
                        </div>
                        <span class="d-none d-md-inline"><?= htmlspecialchars($adminName) ?></span>
                        <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/admin/2fa/setup">
                                <i class="bi bi-shield-lock me-2"></i>Autenticação 2FA
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/admin/logout">
                                <i class="bi bi-box-arrow-left me-2"></i>Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="nd-content">
            <?php
            if (isset($contentView)) {
                extract($viewData ?? []);
                require $contentView;
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Notification Badge Update -->
    <script>
    (function() {
        async function updateNotificationBadge() {
            try {
                const response = await fetch('/admin/api/notifications');
                const data = await response.json();
                const badge = document.getElementById('notificationBadge');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count > 9 ? '9+' : data.count;
                    badge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            } catch (e) {
                console.error('Error fetching notifications:', e);
            }
        }
        updateNotificationBadge();
        setInterval(updateNotificationBadge, 60000);
    })();
    </script>
</body>

</html>