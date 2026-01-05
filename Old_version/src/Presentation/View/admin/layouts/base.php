<?php

/** @var string $pageTitle */
/** @var array $viewData */
$branding = $viewData['branding'] ?? ($branding ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']       ?? 'NimbusDocs';
$primary  = $branding['primary_color']  ?? '#00205b';
$accent   = $branding['accent_color']   ?? '#ffc20e';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($appName . ' — ' . ($pageTitle ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --brand: <?= $primary ?>;
            --brand-600: <?= $accent ?>;
        }

        body {
            background-color: #f5f6f8;
        }

        .admin-navbar,
        .admin-sidebar {
            background: var(--brand);
        }

        .admin-navbar .navbar-brand,
        .admin-navbar .nav-link,
        .admin-navbar .navbar-text {
            color: #fff !important;
        }

        .badge-brand {
            background-color: var(--brand-600);
            color: #000;
        }

        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
        }

        .btn-brand:hover {
            opacity: .9;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
                <?php
                if (isset($contentView)) {
                    extract($viewData ?? []);
                    require $contentView;
                }
                ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>