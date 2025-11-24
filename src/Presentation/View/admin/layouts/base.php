<?php

/**
 * Layout base para o módulo administrativo.
 *
 * Espera as variáveis:
 * - string $pageTitle   Título da página
 * - string $contentView Caminho do arquivo de view a ser incluído
 * - array  $viewData    (opcional) Dados adicionais para a view
 */

$pageTitle   = $pageTitle   ?? 'NimbusDocs Admin';
$contentView = $contentView ?? null;
$viewData    = $viewData    ?? [];

// Extrai as variáveis de $viewData para uso direto na view (com cuidado)
if (!empty($viewData) && is_array($viewData)) {
    extract($viewData, EXTR_SKIP);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5.3.8 via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Espaço para CSS próprio no futuro -->
</head>

<body class="bg-light">

    <?php require __DIR__ . '/../partials/header.php'; ?>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container py-4">
        <?php
        if ($contentView && is_file($contentView)) {
            require $contentView;
        } else {
            echo '<p class="text-muted">Nenhum conteúdo definido para esta página.</p>';
        }
        ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>