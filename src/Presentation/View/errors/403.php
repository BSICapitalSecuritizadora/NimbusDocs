<?php
http_response_code(403);
$pageTitle = 'Acesso Negado';
// Branding config fallback
$branding = $config['branding'] ?? [];
$appName = $branding['app_name'] ?? 'NimbusDocs';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle . ' - ' . $appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
</head>
<body>
    <div class="nd-full-page">
        <!-- Floating Shapes -->
        <div class="nd-shape nd-shape-1"></div>
        <div class="nd-shape nd-shape-2"></div>
        <div class="nd-shape nd-shape-3"></div>

        <div class="nd-glass-card nd-page-card-anim text-center">
            <!-- Icon -->
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle nd-float-anim" 
                     style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                    <i class="bi bi-shield-x text-danger" style="font-size: 2.5rem; color: var(--nd-danger) !important;"></i>
                </div>
            </div>

            <!-- Error Code -->
            <h1 class="display-1 fw-bold text-dark mb-0" style="color: var(--nd-navy-900);">403</h1>
            <h2 class="h4 text-muted mb-4">Permissão Negada</h2>

            <!-- Message -->
            <p class="text-secondary mb-4">
                Sua credencial atual não possui nível de acesso suficiente para este recurso. 
                Por favor, contate o administrador do sistema.
            </p>

            <!-- Actions -->
            <div class="d-flex flex-column gap-2">
                <a href="/admin/dashboard" class="nd-btn nd-btn-primary w-100 justify-content-center">
                    <i class="bi bi-grid-1x2-fill"></i>
                    Retornar ao Painel
                </a>
                <a href="javascript:history.back()" class="nd-btn nd-btn-outline w-100 justify-content-center">
                    <i class="bi bi-arrow-left"></i>
                    Voltar Página
                </a>
            </div>
            
            <div class="mt-4 pt-4 border-top">
                <small class="text-muted d-block">
                    ID do Incidente: <span class="fw-bold text-dark">SEC-<?= rand(1000, 9999) ?></span>
                </small>
            </div>
        </div>
    </div>
</body>
</html>
