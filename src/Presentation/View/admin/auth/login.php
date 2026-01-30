<?php
/**
 * Login Administrativo - Layout Premium Financeiro
 * @var string|null $errorMessage
 * @var string|null $oldEmail  
 * @var string $csrfToken
 */

$appName = $branding['app_name'] ?? 'BSI Capital Securitizadora';
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
    <link href="<?= ($config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css?v=<?= time() ?>" rel="stylesheet">
    
    <!-- Custom Branding -->
    <?php
    $primary = $branding['primary_color'] ?? '#112240'; // Rich Navy
    $accent  = $branding['accent_color']  ?? '#d4a84b'; // Gold
    
    if (class_exists(\App\Support\ColorUtils::class)) {
        $primaryDark  = \App\Support\ColorUtils::adjustBrightness($primary, -20);
        $primaryLight = \App\Support\ColorUtils::adjustBrightness($primary, 20);
        $accentDark   = \App\Support\ColorUtils::adjustBrightness($accent, -20);
    } else {
        $primaryDark  = '#0a192f';
        $primaryLight = '#1d3557';
        $accentDark   = '#b38b36';
    }
    ?>
    <style>
        :root {
            /* Branding Injection */
            --nd-navy-900: <?= $primary ?>;      /* Primary */
            --nd-navy-950: <?= $primaryDark ?>;  /* Darker */
            --nd-navy-800: <?= $primaryLight ?>; /* Lighter */
            --nd-gold-500: <?= $accent ?>;
            --nd-gold-600: <?= $accentDark ?>;
        }
        
        .text-navy-900 { color: var(--nd-navy-900) !important; }
        .bg-navy-900 { background-color: var(--nd-navy-900) !important; }
        
        /* Button Gradient */
        .nd-btn-primary { 
            background: linear-gradient(135deg, var(--nd-navy-800) 0%, var(--nd-navy-900) 100%); 
            border: none; 
            color: #ffffff;
        }
        
        /* Logo Gradient & Style */
        .nd-login-logo { 
            animation: float 4s ease-in-out infinite; 
            margin: 0 auto 1.5rem; 
            width: 64px; 
            height: 64px; 
            background: linear-gradient(135deg, var(--nd-navy-900) 0%, var(--nd-navy-800) 100%); 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 2rem; 
            color: var(--nd-gold-500); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
        }
    </style>
</head>
<body class="nd-login-page">
    <!-- Floating Shapes -->
    
    <div class="nd-login-card">
            <!-- Logo -->
            <div class="nd-login-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
                </svg>
            </div>
            
            <!-- Title -->
            <div class="text-center mb-4">
                <h1 class="h4 fw-bold mb-1 text-navy-900">BSI Capital Securitizadora</h1>
                <p class="text-muted small text-uppercase ls-1">NimbusDocs</p>
            </div>
            
            <!-- Error Alert -->
            <?php $error = $errorMessage ?? \App\Support\Session::getFlash('error'); ?>
            <?php if (!empty($error)): ?>
                <div class="nd-alert nd-alert-danger" id="alertError">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mt-1 flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div><?= htmlspecialchars($error) ?></div>
                    <button type="button" class="nd-alert-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854z"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form action="/admin/login" method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                
                <div class="mb-3">
                    <label for="email" class="nd-label">E-mail Corporativo</label>
                    <div class="nd-input-group">
                        <input type="email" 
                               class="nd-input" 
                               id="email" 
                               name="email" 
                               placeholder="usuario@bsicapital.com.br"
                               value="<?= htmlspecialchars($oldEmail ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               required 
                               autofocus>
                        <div class="nd-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="nd-label">Senha</label>
                    <div class="nd-input-group">
                        <input type="password" 
                               class="nd-input" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required>
                        <div class="nd-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mb-4"></div> <!-- Spacer instead of br -->

                <button type="submit" class="nd-btn nd-btn-primary w-100 py-3 shadow-sm hover-translate-up">
                    <span class="me-2">ACESSAR PLATAFORMA</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 0h-8A1.5 1.5 0 0 0 0 1.5v9A1.5 1.5 0 0 0 1.5 12h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                </button>
            </form>
            
            <div class="w-100 text-center mt-3">
                <a href="/admin/forgot-password" class="text-decoration-none text-muted small hover-text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1 mb-1" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                    </svg>
                    Esqueceu sua senha?
                </a>
            </div>
            
            <!-- Footer Divider -->
            <div class="d-flex align-items-center justify-content-center my-4 w-100">
                <div class="border-top border-secondary opacity-25 flex-grow-1"></div>
                <span class="px-3 text-muted x-small fw-semibold text-uppercase">ou</span>
                <div class="border-top border-secondary opacity-25 flex-grow-1"></div>
            </div>
            
            <a href="/admin/login/microsoft" class="nd-btn nd-btn-ghost w-100 gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7.462 0H0v7.19h7.462V0zM16 0H8.538v7.19H16V0zM7.462 8.211H0V16h7.462V8.211zm8.538 0H8.538V16H16V8.211z"/>
                </svg>
                <span class="text-dark fw-medium text-uppercase small ls-1">Entrar com Microsoft</span>
            </a>
            
            <p class="text-center mt-auto mb-0 x-small text-muted opacity-75">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Acesso Restrito.
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>