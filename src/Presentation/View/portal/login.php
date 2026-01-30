<?php
/**
 * Login Portal - Layout Premium Financeiro
 * @var string $csrfToken
 * @var array $flash
 */
$error   = $flash['error']   ?? null;
$success = $flash['success'] ?? null;
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name'] ?? 'NimbusDocs';

$bsiLogo = "https://media.licdn.com/dms/image/v2/D4D0BAQExaECDvucniw/company-logo_200_200/B4DZbwVBVeG0AI-/0/1747788764990/bsi_capital_securitizadora_s_a_logo?e=2147483647&v=beta&t=NwW3hFxem07njQLPtUFvIAOnOeq_tsRDcli7lc8drrI";
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
    <link href="<?= ($config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css" rel="stylesheet">
    
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
        <!-- Header -->
        <div class="text-center mb-5">
            <div class="logo-container">
                <img src="<?= $bsiLogo ?>" alt="BSI Capital" style="width: 50px; height: 50px; object-fit: contain; filter: brightness(0) invert(1);">
            </div>
            
            <h1 class="h4 fw-bold text-white mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: 0.5px;">Portal do Cliente</h1>
            <p class="text-white-50 small mb-0 x-small text-uppercase ls-2">BSI Capital Securitizadora</p>
        </div>
        
        <!-- Messages -->
        <?php if ($error): ?>
            <div class="nd-alert nd-alert-danger mb-4 border-0 bg-danger-subtle text-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div class="ms-2"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="nd-alert nd-alert-success mb-4 border-0 bg-success-subtle text-success" role="alert">
                <i class="bi bi-check-circle-fill mt-1"></i>
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
                <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
        
        <div class="mt-5 text-center px-4">
            <div class="d-flex align-items-center mb-4 opacity-25">
                <div class="flex-grow-1 border-top border-light"></div>
                <div class="mx-2 text-light small"><i class="bi bi-lock-fill"></i></div>
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