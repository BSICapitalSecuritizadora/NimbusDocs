<?php
/**
 * Login Portal - Layout Premium
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
    
    <style>
        body {
            background-color: var(--nd-navy-900);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, hsla(253,16%,7%,0) 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, hsla(225,39%,30%,0) 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, hsla(339,49%,30%,0) 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .nd-glass-card {
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }

        .nd-input-code {
            font-family: 'Inter', monospace;
            font-size: 1.1rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            background: rgba(0, 0, 0, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        
        .nd-input-code:focus {
            background: rgba(0, 0, 0, 0.3) !important;
            border-color: var(--nd-gold-500) !important;
            box-shadow: 0 0 0 4px rgba(234, 179, 8, 0.15) !important;
        }
        
        .nd-input-code::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
            opacity: 1;
        }

        .bg-logo {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(4px);
        }

        /* Ambient effects */
        .ambient-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.08) 0%, rgba(0,0,0,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    
    <div class="ambient-glow"></div>

    <div class="nd-glass-card fade-in-up">
        <!-- Logo -->
        <div class="text-center mb-5">
            <div class="bg-logo d-inline-flex align-items-center justify-content-center p-3 rounded-4 mb-4 shadow-lg">
                <img src="<?= $bsiLogo ?>" alt="BSI Capital" class="rounded-3" style="width: 64px; height: 64px; object-fit: contain;">
            </div>
            <h1 class="h4 fw-bold text-white mb-1 ls-1">Acesso ao Portal</h1>
            <p class="text-white-50 small mb-0">BSI Capital Securitizadora</p>
        </div>
        
        <!-- Alerts -->
        <?php if ($error): ?>
            <div class="alert alert-danger border-0 bg-danger-subtle text-danger d-flex align-items-center rounded-3 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success border-0 bg-success-subtle text-success d-flex align-items-center rounded-3 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form method="post" action="/portal/login" autocomplete="off">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="mb-4">
                <label for="access_code" class="form-label text-white-50 text-uppercase x-small fw-bold ls-2 mb-2">Código de Acesso</label>
                <div class="position-relative">
                    <input type="text"
                           class="form-control form-control-lg nd-input-code text-center py-3 rounded-3"
                           id="access_code"
                           name="access_code"
                           placeholder="ABCD-1234-EFGH"
                           autocomplete="off"
                           required
                           autofocus
                           maxlength="14">
                </div>
            </div>

            <button type="submit" class="btn nd-btn-gold w-100 py-3 rounded-3 fw-bold shadow-lg hover-scale text-uppercase ls-1">
                <i class="bi bi-shield-lock-fill me-2"></i> Acessar Portal
            </button>
        </form>
        
        <div class="mt-5 text-center">
            <p class="text-white-50 x-small mb-0 opacity-75">
                Utilize o código enviado para seu e-mail ou telefone.<br>
                Em caso de dúvidas, contate o suporte.
            </p>
        </div>
    </div>

    <!-- Mask Script -->
    <script>
        document.getElementById('access_code').addEventListener('input', function (e) {
            let target = e.target;
            let input = target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // Format: XXXX-XXXX-XXXX
            let formatted = '';
            if (input.length > 0) formatted += input.substring(0, 4);
            if (input.length > 4) formatted += '-' + input.substring(4, 8);
            if (input.length > 8) formatted += '-' + input.substring(8, 12);
            
            target.value = formatted;
        });
    </script>
</body>
</html>