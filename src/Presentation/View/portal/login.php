<?php
/**
 * Login Portal - Layout Premium Financeiro
 * @var string $csrfToken
 * @var array $flash
 */
$error   = $flash['error']   ?? null;
$success = $flash['success'] ?? null;
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name'] ?? 'BSI Capital Securitizadora';

$bsiLogo = ""; // Removed external dependency
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Cliente - <?= htmlspecialchars($appName) ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ($config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css?v=<?= time() ?>" rel="stylesheet">
    
    <!-- Custom Branding -->
    <?php
    $primary = $branding['primary_color'] ?? '#0a1628';
    $accent  = $branding['accent_color']  ?? '#d4a84b';
    
    if (class_exists(\App\Support\ColorUtils::class)) {
        $p900 = \App\Support\ColorUtils::adjustBrightness($primary, -20);
        $p800 = $primary;
        $g500 = $accent;
        $g600 = \App\Support\ColorUtils::adjustBrightness($accent, -20);
    } else {
        $p900 = $primary; $p800 = $primary;
        $g500 = $accent; $g600 = $accent;
    }
    ?>
    <style>
        :root {
            --nd-navy-900: <?= $p900 ?>;
            --nd-navy-800: <?= $p800 ?>;
            --nd-gold-500: <?= $g500 ?>;
        }

        /* Portal specific login overrides */
        .nd-login-page {
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, hsla(253,16%,7%,0) 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, hsla(339,49%,30%,0) 50%);
        }

        .nd-glass-card {
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }

        .nd-input-code {
            font-family: 'Inter', monospace;
            font-size: 1.25rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.2) !important;
            border: 2px solid rgba(255, 255, 255, 0.1) !important;
            color: var(--nd-white) !important;
            height: 64px;
            transition: all 0.3s ease;
        }
        
        .nd-input-code:focus {
            background: rgba(0, 0, 0, 0.4) !important;
            border-color: var(--nd-gold-500) !important;
            box-shadow: 0 0 0 4px rgba(212, 168, 75, 0.15) !important;
            transform: scale(1.02);
        }
        
        .nd-input-code::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .ambient-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(212, 168, 75, 0.06) 0%, rgba(0,0,0,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body class="nd-login-page">
    
    <div class="ambient-glow"></div>

    <div class="nd-glass-card">
        <div class="text-center mb-5">
            <div class="logo-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16">
                    <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.935.536.27.111.554.207.818.207.265 0 .548-.096.818-.207.283-.116.59-.292.935-.536a10.728 10.728 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453c-.386.273-.744.482-1.048.625-.28.132-.581.24-.829.24s-.548-.108-.829-.24a11.777 11.777 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                    <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                </svg>
            </div>
            
            <h1 class="h4 fw-bold text-white mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: 0.5px;">Portal do Cliente</h1>
            <p class="text-white-50 small mb-0 x-small text-uppercase ls-2">BSI Capital Securitizadora</p>
        </div>
        
        <!-- Messages -->
        <?php if ($error): ?>
            <div class="nd-alert nd-alert-danger mb-4 border-0 bg-danger-subtle text-danger" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mt-1 flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div class="ms-2"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="nd-alert nd-alert-success mb-4 border-0 bg-success-subtle text-success" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mt-1 flex-shrink-0" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <div class="ms-2"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form method="post" action="/portal/login" autocomplete="off">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="mb-5">
                <label for="access_code" class="d-block text-center text-gold-300 text-uppercase x-small fw-bold ls-2 mb-3">
                    Código de Acesso
                </label>
                <div class="position-relative">
                    <input type="text"
                           class="form-control nd-input-code text-center"
                           id="access_code"
                           name="access_code"
                           placeholder="XXXX-XXXX-XXXX"
                           autocomplete="off"
                           required
                           autofocus
                           maxlength="14">
                </div>
            </div>

            <button type="submit" class="nd-btn nd-btn-gold w-100 py-3 fw-bold text-uppercase ls-1 shadow-lg hover-scale">
                <span class="fs-6">Acessar Portal</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
            </button>
        </form>
        
        <div class="mt-5 text-center px-4">
            <div class="d-flex align-items-center mb-4 opacity-25">
                <div class="flex-grow-1 border-top border-light"></div>
                <div class="mx-2 text-light small">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    </svg>
                </div>
                <div class="flex-grow-1 border-top border-light"></div>
            </div>
            <p class="text-white-50 x-small mb-0 opacity-75 lh-sm">
                Ambiente seguro protegido por criptografia de ponta a ponta.
            </p>
        </div>
    </div>

    <!-- Layout Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Access Code Formatter
        document.getElementById('access_code').addEventListener('input', function (e) {
            let target = e.target;
            let input = target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            let formatted = '';
            if (input.length > 0) formatted += input.substring(0, 4);
            if (input.length > 4) formatted += '-' + input.substring(4, 8);
            if (input.length > 8) formatted += '-' + input.substring(8, 12);
            
            target.value = formatted;
        });
    </script>
</body>
</html>