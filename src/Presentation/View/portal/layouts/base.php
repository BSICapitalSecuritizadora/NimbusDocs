<?php

/** @var string $pageTitle */
/** @var string $contentView */
/** @var array  $viewData */

extract($viewData, EXTR_SKIP);

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Portal do Usuário', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5.3.8 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light">
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <main class="container py-4">
        <?php require $contentView; ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>