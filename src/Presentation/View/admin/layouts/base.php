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

// Check if we need simple brightness adjustments
// If App\Support\ColorUtils doesn't exist, we use the raw color.
if (class_exists(\App\Support\ColorUtils::class)) {
    // Generate palette based on primary
    $p900 = \App\Support\ColorUtils::adjustBrightness($primary, -20);
    $p800 = $primary;
    $p700 = \App\Support\ColorUtils::adjustBrightness($primary, 10);
    $p500 = \App\Support\ColorUtils::adjustBrightness($primary, 40);
    
    // Generate accent palette
    $g500 = $accent;
    $g400 = \App\Support\ColorUtils::adjustBrightness($accent, 10);
} else {
    $p900 = $primary; $p800 = $primary; $p700 = $primary; $p500 = $primary;
    $g500 = $accent; $g400 = $accent;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($appName . ' — ' . ($pageTitle ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link href="<?= ($config['asset_url'] ?? '') ?>/assets/fonts/fonts.css" rel="stylesheet">
    
    <!-- Vendor CSS -->
    <link href="<?= ($config['asset_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ($config['asset_url'] ?? '') ?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link href="<?= ($config['asset_url'] ?? '') ?>/css/nimbusdocs-theme.css?v=<?= time() ?>" rel="stylesheet">

    
    <!-- Custom Branding Injection -->
    <style>
        :root {
            /* Overlay Brand Colors if customized */
            --nd-navy-900: <?= $p900 ?>;
            --nd-navy-800: <?= $p800 ?>;
            --nd-navy-700: <?= $p700 ?>;
            --nd-navy-500: <?= $p500 ?>;
            
            --nd-gold-500: <?= $g500 ?>;
            --nd-gold-400: <?= $g400 ?>;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="nd-main">
        <!-- Header -->
        <header class="nd-header">
            <div class="nd-header-title">
                <?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Search -->
                <form action="/admin/search" method="GET" class="d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" 
                               name="q" 
                               class="nd-input" 
                               placeholder="Pesquisar..." 
                               style="width: 240px; padding-left: 2.25rem; height: 38px; font-size: 0.875rem;">
                        <i class="bi bi-search position-absolute" 
                           style="left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--nd-gray-400);"></i>
                    </div>
                </form>
                
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="nd-btn nd-btn-ghost p-2 position-relative rounded-circle" 
                            type="button" 
                            data-bs-toggle="dropdown"
                            style="width: 38px; height: 38px;">
                        <i class="bi bi-bell" style="font-size: 1.1rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" 
                              style="font-size: 0.6rem; padding: 0.25em 0.4em;" 
                              id="notificationBadge">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 300px;">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold">Notificações</h6></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><span class="dropdown-item-text text-muted small py-3 text-center d-block">Nenhuma notificação recente</span></li>
                    </ul>
                </div>
                
                <!-- Divider -->
                <div class="vr h-50 mx-1 bg-secondary opacity-25"></div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="d-flex align-items-center gap-2 border-0 bg-transparent p-0" 
                            type="button" 
                            data-bs-toggle="dropdown">
                        <div class="nd-avatar" style="width: 38px; height: 38px;">
                            <?= htmlspecialchars($adminInitials) ?>
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.2;"><?= htmlspecialchars($adminName) ?></div>
                            <div class="text-muted" style="font-size: 0.7rem;">Administrador</div>
                        </div>
                        <i class="bi bi-chevron-down text-muted ms-1" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                        <li>
                            <a class="dropdown-item py-2" href="/admin/2fa/setup">
                                <i class="bi bi-shield-lock me-2 text-primary"></i>Segurança 2FA
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="/admin/logout">
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

    <!-- Scripts (Local) -->
    <script src="<?= ($config['asset_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= ($config['asset_url'] ?? '') ?>/js/nimbusdocs-utils.js"></script>
    
    <script>
    (function() {
        // Simple badge updater
        async function updateNotificationBadge() {
            try {
                const response = await fetch('/admin/api/notifications');
                if (!response.ok) return;
                const data = await response.json();
                const badge = document.getElementById('notificationBadge');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count > 9 ? '9+' : data.count;
                    badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                }
            } catch (e) {
                // Silent fail
            }
        }
        updateNotificationBadge();
        setInterval(updateNotificationBadge, 60000);
    })();
    </script>
</body>
</html>