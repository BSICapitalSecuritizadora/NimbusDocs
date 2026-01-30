<?php
/**
 * Login Administrativo - Layout Premium Financeiro
 * @var string|null $errorMessage
 * @var string|null $oldEmail  
 * @var string $csrfToken
 */

$appName = $branding['app_name'] ?? 'NimbusDocs';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - <?= htmlspecialchars($appName) ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ($config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css" rel="stylesheet">
    
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
        
        /* Local specific tweaks that didn't warrant global CSS */
        .nd-shape-1 { width: 300px; height: 300px; top: 10%; right: 10%; animation: float 6s ease-in-out infinite; background: var(--nd-gold-500); position: absolute; border-radius: 50%; opacity: 0.05; }
        .nd-shape-2 { width: 200px; height: 200px; bottom: 20%; left: 5%; animation: float 8s ease-in-out infinite reverse; background: var(--nd-navy-500); position: absolute; border-radius: 50%; opacity: 0.1; }
        .nd-shape-3 { width: 150px; height: 150px; top: 50%; left: 20%; animation: float 5s ease-in-out infinite; background: var(--nd-gold-500); position: absolute; border-radius: 50%; opacity: 0.05; }
        
        .nd-login-logo { animation: float 4s ease-in-out infinite; margin: 0 auto 1.5rem; width: 64px; height: 64px; background: linear-gradient(135deg, var(--nd-navy-900) 0%, var(--nd-navy-800) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--nd-gold-500); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="nd-login-page">
        <!-- Floating Shapes -->
        <div class="nd-shape-1"></div>
        <div class="nd-shape-2"></div>
        <div class="nd-shape-3"></div>
        
        <div class="nd-login-card">
            <!-- Logo -->
            <div class="nd-login-logo">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            
            <!-- Title -->
            <div class="text-center mb-4">
                <h1 class="h4 fw-bold mb-1 col-navy-900"><?= htmlspecialchars($appName) ?></h1>
                <p class="text-muted small text-uppercase ls-1">Portal Corporativo</p>
            </div>
            
            <!-- Error Alert -->
            <?php $error = $errorMessage ?? \App\Support\Session::getFlash('error'); ?>
            <?php if (!empty($error)): ?>
                <div class="nd-alert nd-alert-danger" id="alertError">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <span class="flex-fill"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                    <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="post" action="/admin/login">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="mb-3">
                    <label for="email" class="nd-label">E-mail Corporativo</label>
                    <div class="position-relative">
                        <input type="email" 
                               class="nd-input ps-5" 
                               id="email" 
                               name="email" 
                               placeholder="usuario@empresa.com.br"
                               value="<?= htmlspecialchars($oldEmail ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               required 
                               autofocus>
                        <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="nd-label">Senha</label>
                    <div class="position-relative">
                        <input type="password" 
                               class="nd-input ps-5" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required>
                        <i class="bi bi-key position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>
                </div>

                <button type="submit" class="nd-btn nd-btn-primary w-100 py-3 shadow-sm hover-translate-up">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Acessar Plataforma
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="/admin/forgot-password" class="text-decoration-none text-muted small hover-text-navy">
                    <i class="bi bi-question-circle me-1"></i>Esqueceu sua senha?
                </a>
            </div>
            
            <!-- Footer Divider -->
            <div class="d-flex align-items-center my-4">
                <div class="flex-grow-1 border-top border-secondary-subtle"></div>
                <span class="px-3 text-muted x-small fw-semibold text-uppercase">ou</span>
                <div class="flex-grow-1 border-top border-secondary-subtle"></div>
            </div>
            
            <a href="/admin/login/microsoft" class="nd-btn nd-btn-ghost w-100 gap-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" alt="Microsoft" style="width: 18px; height: 18px;">
                <span class="text-dark fw-medium">Entrar com Microsoft</span>
            </a>
            
            <p class="text-center mt-5 mb-0 x-small text-muted opacity-75">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Acesso Restrito.
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>