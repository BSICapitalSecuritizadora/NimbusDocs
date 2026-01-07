<?php
$branding = $viewData['branding'] ?? ($branding ?? ($config['branding'] ?? []));
$appName  = $branding['app_name'] ?? 'NimbusDocs';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($appName . ' — ' . ($pageTitle ?? 'Portal'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
    
    <style>
        /* Layout */
        body {
            background-color: var(--nd-gray-100);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .portal-main {
            flex: 1;
            padding: 2.5rem 0;
        }

        /* Navbar Premium */
        .portal-navbar {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            /* Backdrop filter se o browser suportar */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .portal-navbar .navbar-brand {
            color: var(--nd-white) !important;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 1.25rem;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--nd-gold-400);
            font-size: 1.2rem;
        }

        .portal-navbar .nav-link {
            color: rgba(255, 255, 255, 0.7) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            border-radius: 50rem;
            transition: all 0.2s ease;
        }

        .portal-navbar .nav-link:hover {
            color: var(--nd-white) !important;
            background: rgba(255,255,255,0.05);
        }

        .portal-navbar .nav-link.active {
            color: var(--nd-white) !important;
            background: rgba(255,255,255,0.15);
            font-weight: 600;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
        }

        /* User Menu */
        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--nd-gold-500) !important;
            color: var(--nd-navy-900);
            font-weight: 700;
            font-size: 0.85rem;
        }
        
        .user-menu-toggle {
            padding: 0.25rem;
            border-radius: 50rem;
            transition: background 0.2s;
        }
        
        .user-menu-toggle:hover,
        .user-menu-toggle.show {
            background: rgba(255,255,255,0.1);
        }

        .dropdown-menu {
            animation: fadeIn 0.2s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="portal-main">
        <div class="container">
            <?php
            if (isset($contentView)) {
                extract($viewData ?? []);
                require $contentView;
            }
            ?>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>