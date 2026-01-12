<?php
/**
 * Login Portal - Layout Premium
 * @var string $csrfToken
 * @var array $flash
 */
$error   = $flash['error']   ?? null;
$success = $flash['success'] ?? null;
$oldIdentifier = $flash['old_identifier'] ?? '';

// Branding setup (fallback)
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name'] ?? 'NimbusDocs';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Cliente - <?= htmlspecialchars($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
    <style>
        .nd-input-code {
            font-family: monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="nd-full-page">
        <!-- Floating Shapes -->
        <div class="nd-shape nd-shape-1"></div>
        <div class="nd-shape nd-shape-2"></div>
        <div class="nd-shape nd-shape-3"></div>
        
        <div class="nd-glass-card nd-page-card-anim">
            <!-- Logo -->
            <div class="d-flex justify-content-center mb-4">
                <div class="nd-login-logo mb-0 overflow-hidden d-flex align-items-center justify-content-center bg-white">
                    <?php if (!empty($branding['portal_logo_url'])): ?>
                        <img src="<?= htmlspecialchars($branding['portal_logo_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Logo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <img src="/assets/images/logo.jpg" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0iIzc4MzUwZiIgY2xhc3M9ImJpIGJpLWJ1aWxkaW5ncy1maWxsIj48cGF0aCBkPSJNMTUuNSAyaC0xMC41YTEgMSAwIDAgMC0xIDF2MTMuNWEwLjUgMC41IDAgMCAwIC41LjVoMTFhMC41IDAuNSAwIDAgMCAuNS0uNXYtMTMuNWExIDEgMCAwIDAtMS0xem0tMS41IDFoLjl2MWgtLjl2LTF6bTAgMy41aC45djFoLS45di0xeiIvPjwvc3ZnPg=='" alt="Logo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Title -->
            <h1 class="nd-login-title text-center">BSI Capital Documentos</h1>
            
            <!-- Alerts -->
            <?php if ($error): ?>
                <div class="nd-alert nd-alert-danger" id="alertError">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                    <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="nd-alert nd-alert-success" id="alertSuccess">
                    <i class="bi bi-check-circle-fill"></i>
                    <span class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
                    <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Form -->
            <form method="post" action="/portal/login">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="mb-4">
                    <label for="access_code" class="nd-label text-center d-block">Código de Acesso</label>
                    <div class="nd-input-group">
                        <input type="text"
                               class="nd-input nd-input-code text-center ps-5 pe-5"
                               id="access_code"
                               name="access_code"
                               placeholder="ABCD-1234-EFGH"
                               autocomplete="off"
                               required
                               autofocus>
                        <i class="bi bi-key-fill nd-input-icon start-0 ms-3" style="left: 10px;"></i>
                    </div>
                </div>

                <button type="submit" class="nd-btn nd-btn-gold nd-btn-lg w-100">
                    <i class="bi bi-shield-lock-fill me-2"></i>
                    Entrar
                </button>
            </form>
            
            <div class="mt-4 pt-3 border-top border-light-subtle text-center">
                <p class="nd-login-footer mb-0">
                    Informe o código recebido para continuar.<br>
                    Precisa de ajuda? Fale com nosso suporte.
                </p>
            </div>
        </div>
    </div>
</body>
</html>