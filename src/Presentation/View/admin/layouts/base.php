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

// Garante que `$admin` esteja definido para os parciais (header/sidebar)
// mesmo que o controller não o tenha passado explicitamente.
if (!isset($admin)) {
    $admin = \App\Support\Session::get('admin') ?? null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Admin' ?> — NimbusDocs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin-left: 240px;
        }

        @media (max-width: 768px) {
            body {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="p-4">
        <?php if (isset($contentView)) {
            extract($viewData ?? []);
            require $contentView;
        } ?>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

</body>

</html>