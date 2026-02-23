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
    
    <!-- Local Fonts (System Font Stack) -->
    <link href="<?= ($config['asset_url'] ?? '') ?>/assets/fonts/fonts.css" rel="stylesheet">
    
    <!-- Styles (Local) -->
    <link href="<?= ($config['asset_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ($config['asset_url'] ?? '') ?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= ($config['asset_url'] ?? '') ?>/css/nimbusdocs-theme.css" rel="stylesheet">

    
    <!-- Custom Branding -->
    <?php
    $primary = $branding['primary_color'] ?? '#0a1628';
    $accent  = $branding['accent_color']  ?? '#d4a84b';
    
    if (class_exists(\App\Support\ColorUtils::class)) {
        $p900 = \App\Support\ColorUtils::adjustBrightness($primary, -20);
        $p800 = $primary;
        $p700 = \App\Support\ColorUtils::adjustBrightness($primary, 20);
        $g500 = $accent;
        $g600 = \App\Support\ColorUtils::adjustBrightness($accent, -20);
    } else {
        $p900 = $primary; $p800 = $primary; $p700 = $primary;
        $g500 = $accent; $g600 = $accent;
    }
    ?>
    <style>
        :root {
            /* Branding Injection */
            --nd-navy-900: <?= $p900 ?>;
            --nd-navy-800: <?= $p800 ?>;
            --nd-navy-700: <?= $p700 ?>;
            
            --nd-gold-500: <?= $g500 ?>;
            --nd-gold-600: <?= $g600 ?>;
        }

        body {
            background-color: var(--nd-surface-50);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .portal-main {
            flex: 1;
            padding: 3rem 0; /* More spacing for portal */
        }
    </style>
</head>
<body>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="portal-main">
        <div class="container-xxl"> <!-- Use XXL container for wider layout -->
            <?php
            if (isset($contentView)) {
                extract($viewData ?? []);
                require $contentView;
            }
            ?>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <script src="<?= ($config['asset_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= ($config['asset_url'] ?? '') ?>/js/nimbusdocs-utils.js"></script>

</body>
</html>