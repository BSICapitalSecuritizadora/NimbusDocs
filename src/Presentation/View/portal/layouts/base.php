<?php

/** @var string $pageTitle */
/** @var string $contentView */
/** @var array  $viewData */

extract($viewData, EXTR_SKIP);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Portal' ?> — NimbusDocs</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons (se quiser usar ícones no portal também) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6f8;
        }

        .portal-navbar {
            background: #00205b;
            /* azul BSI */
        }

        .portal-navbar .navbar-brand,
        .portal-navbar .nav-link,
        .portal-navbar .navbar-text {
            color: #ffffff !important;
        }

        .portal-navbar .nav-link.active {
            font-weight: 600;
            text-decoration: underline;
        }

        .portal-main {
            padding-top: 1.5rem;
            padding-bottom: 2rem;
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