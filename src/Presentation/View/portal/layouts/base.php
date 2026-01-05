<?php
$branding = $viewData['branding'] ?? ($branding ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']       ?? 'NimbusDocs';
$primary  = $branding['primary_color']  ?? '#00205b';
$accent   = $branding['accent_color']   ?? '#ffc20e';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($appName . ' — ' . ($pageTitle ?? 'Portal'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap, etc... -->
    <style>
        :root {
            --brand: <?= $primary ?>;
            --brand-600: <?= $accent ?>;
        }

        body {
            background-color: #f5f6f8;
        }

        .portal-navbar {
            background: var(--brand);
        }

        .portal-navbar .navbar-brand,
        .portal-navbar .nav-link,
        .portal-navbar .navbar-text {
            color: #fff !important;
        }
    </style>
</head>

<body>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="portal-main">
        <div class="container">
            <?php
            if (isset($contentView)) {
                // $viewData vem do controller
                extract($viewData ?? []);
                require $contentView;
            }
            ?>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <!-- Bootstrap JS (se precisar de componentes dinâmicos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>