<?php
/**
 * Login Administrativo - Design Premium Financeiro v2.0
 * BSI Capital Securitizadora
 * 
 * @var string|null $errorMessage
 * @var string|null $oldEmail  
 * @var string $csrfToken
 */

$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName = $branding['app_name'] ?? 'BSI Capital Securitizadora';
$primary = $branding['primary_color'] ?? '#0c1b2e';
$accent  = $branding['accent_color']  ?? '#d4a84b';

// Generate color variants
if (class_exists(\App\Support\ColorUtils::class)) {
    $p900 = \App\Support\ColorUtils::adjustBrightness($primary, -15);
    $p800 = $primary;
    $p700 = \App\Support\ColorUtils::adjustBrightness($primary, 15);
    $g500 = $accent;
    $g400 = \App\Support\ColorUtils::adjustBrightness($accent, 15);
} else {
    $p900 = '#06101c'; $p800 = $primary; $p700 = '#1a2f4e';
    $g500 = $accent; $g400 = '#e4c47a';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Administrativo - <?= htmlspecialchars($appName) ?>">
    <title>Login Administrativo - <?= htmlspecialchars($appName) ?></title>
    
    <!-- Local Fonts (System Font Stack) -->
    <link href="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/assets/fonts/fonts.css" rel="stylesheet">
    
    <!-- Bootstrap + Icons (Local) -->
    <link href="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- NimbusDocs Theme -->
    <link href="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css?v=<?= time() ?>" rel="stylesheet">

    
    <style>
        :root {
            --nd-navy-900: <?= $p900 ?>;
            --nd-navy-800: <?= $p800 ?>;
            --nd-navy-700: <?= $p700 ?>;
            --nd-gold-500: <?= $g500 ?>;
            --nd-gold-400: <?= $g400 ?>;
        }
        
        /* Animated Grid Background */
        .nd-login-bg-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(212, 168, 75, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212, 168, 75, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: 0.5;
        }
        
        /* Floating particles */
        .nd-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--nd-gold-500);
            border-radius: 50%;
            opacity: 0.3;
            animation: particleFloat 20s infinite ease-in-out;
        }
        
        .nd-particle:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .nd-particle:nth-child(2) { top: 60%; left: 80%; animation-delay: 5s; }
        .nd-particle:nth-child(3) { top: 80%; left: 20%; animation-delay: 10s; }
        .nd-particle:nth-child(4) { top: 30%; left: 70%; animation-delay: 15s; }
        
        @keyframes particleFloat {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            25% { transform: translate(30px, -30px) scale(1.5); opacity: 0.6; }
            50% { transform: translate(-20px, 20px) scale(1); opacity: 0.3; }
            75% { transform: translate(40px, 10px) scale(1.2); opacity: 0.5; }
        }
        
        /* Card entrance animation */
        .nd-login-card {
            animation: cardEntrance 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes cardEntrance {
            0% { opacity: 0; transform: translateY(30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        /* Premium divider */
        .nd-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.75rem 0;
        }
        
        .nd-divider::before,
        .nd-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--nd-surface-300), transparent);
        }
        
        .nd-divider span {
            color: var(--nd-gray-400);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Microsoft button */
        .nd-btn-microsoft {
            background: var(--nd-white);
            border: 1px solid var(--nd-surface-300);
            color: var(--nd-gray-700);
            padding: 0.75rem 1.5rem;
        }
        
        .nd-btn-microsoft:hover {
            background: var(--nd-surface-50);
            border-color: var(--nd-gray-400);
            color: var(--nd-gray-800);
            transform: translateY(-1px);
            box-shadow: var(--nd-shadow-sm);
        }
        
        .nd-btn-microsoft svg {
            width: 18px;
            height: 18px;
        }
        
        /* Footer link */
        .nd-footer-link {
            color: var(--nd-gray-500);
            font-size: 0.8125rem;
            transition: var(--nd-transition-fast);
        }
        
        .nd-footer-link:hover {
            color: var(--nd-navy-700);
        }
    </style>
</head>
<body class="nd-login-page">
    <!-- Background Effects -->
    <div class="nd-login-bg-grid"></div>
    <div class="nd-particle"></div>
    <div class="nd-particle"></div>
    <div class="nd-particle"></div>
    <div class="nd-particle"></div>
    
    <!-- Login Card -->
    <div class="nd-login-card">
        <!-- Logo -->
        <div class="nd-login-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
            </svg>
        </div>
        
        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="nd-login-title"><?= htmlspecialchars($appName) ?></h1>
            <p class="nd-login-subtitle">Painel Administrativo</p>
        </div>
        
        <!-- Error Alert -->
        <?php $error = $errorMessage ?? \App\Support\Session::getFlash('error'); ?>
        <?php if (!empty($error)): ?>
            <div class="nd-alert nd-alert-danger mb-4 fade-in" id="alertError">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div class="flex-grow-1"><?= htmlspecialchars($error) ?></div>
                <button type="button" class="nd-alert-close" onclick="this.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854z"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form action="/admin/login" method="POST" id="loginForm" autocomplete="on">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            
            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="nd-label">E-mail</label>
                <div class="nd-input-group">
                    <input type="email" 
                           class="nd-input" 
                           id="email" 
                           name="email" 
                           placeholder="usuario@bsicapital.com.br"
                           value="<?= htmlspecialchars($oldEmail ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                           required 
                           autofocus
                           autocomplete="email">
                    <div class="nd-input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="nd-label">Senha</label>
                <div class="nd-input-group">
                    <input type="password" 
                           class="nd-input" 
                           id="password" 
                           name="password" 
                           placeholder="••••••••" 
                           required
                           autocomplete="current-password">
                    <div class="nd-input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="nd-btn nd-btn-primary w-100 py-3 hover-translate-up">
                <span>ACESSAR PLATAFORMA</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
            </button>
        </form>
        
        <!-- Forgot Password -->
        <div class="text-center mt-3">
            <a href="/admin/forgot-password" class="nd-footer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" style="vertical-align: -0.1em;" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                </svg>
                Esqueceu sua senha?
            </a>
        </div>
        
        <!-- Divider -->
        <div class="nd-divider">
            <span>ou</span>
        </div>
        
        <!-- Microsoft SSO -->
        <a href="/admin/login/microsoft" class="nd-btn nd-btn-microsoft w-100 d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21">
                <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
                <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
                <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
                <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
            </svg>
            <span class="fw-medium">Entrar com Microsoft</span>
        </a>
        
        <!-- Footer -->
        <p class="text-center mt-4 mb-0 x-small text-muted">
            &copy; <?= date('Y') ?> BSI Capital Securitizadora. Acesso Restrito.
        </p>
    </div>

    <!-- Scripts (Local) -->
    <script src="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alert after 5 seconds
        const alertEl = document.getElementById('alertError');
        if (alertEl) {
            setTimeout(() => {
                alertEl.style.opacity = '0';
                alertEl.style.transform = 'translateY(-10px)';
                alertEl.style.transition = 'all 0.3s ease';
                setTimeout(() => alertEl.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>