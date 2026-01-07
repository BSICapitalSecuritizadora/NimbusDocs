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
        body {
            background-color: var(--nd-gray-100);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .portal-main {
            flex: 1;
            padding: 2rem 0;
        }
        
        .portal-navbar {
            background: linear-gradient(90deg, var(--nd-navy-900) 0%, var(--nd-navy-800) 100%);
            box-shadow: var(--nd-shadow-md);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .portal-navbar .navbar-brand {
            color: var(--nd-white) !important;
            font-weight: 600;
        }
        
        .portal-navbar .nav-link {
            color: rgba(255, 255, 255, 0.7) !important;
            font-weight: 500;
            transition: var(--nd-transition);
        }
        
        .portal-navbar .nav-link:hover,
        .portal-navbar .nav-link.active {
            color: var(--nd-white) !important;
        }
        
        .portal-navbar .nav-link.active {
            color: var(--nd-gold-500) !important;
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