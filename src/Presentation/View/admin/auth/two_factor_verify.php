<?php
/**
 * 2FA Verification View (During Login)
 * Matches the premium design system from login.php
 *
 * @var string $csrfToken
 * @var string|null $error
 * @var array $branding
 * @var array $config
 */

$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']     ?? 'BSI Capital Securitizadora';
$primary  = $branding['primary_color'] ?? '#0c1b2e';
$accent   = $branding['accent_color']  ?? '#d4a84b';

// Generate color variants (same logic as login.php)
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
    <meta name="description" content="Verificação em Dois Fatores - <?= htmlspecialchars($appName) ?>">
    <title>Verificação 2FA - <?= htmlspecialchars($appName) ?></title>
    
    <!-- Local Fonts (System Font Stack) -->
    <link href="<?= ($config['base_url'] ?? '') ?>/assets/fonts/fonts.css" rel="stylesheet">
    
    <!-- Styles (Local) -->
    <link href="<?= ($config['base_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ($config['base_url'] ?? '') ?>/assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= ($config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css?v=<?= time() ?>" rel="stylesheet">

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

        /* 2FA-specific token input */
        .nd-token-input {
            font-family: 'JetBrains Mono', 'SF Mono', 'Fira Code', Consolas, monospace;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 0.6rem;
            text-align: center;
            padding: 1rem 1.25rem;
            height: auto;
            color: var(--nd-navy-800);
            border: 2px solid var(--nd-surface-300, #e2e8f0);
            border-radius: 12px;
            background: var(--nd-white, #fff);
            transition: all 0.25s ease;
        }

        .nd-token-input::placeholder {
            color: var(--nd-gray-300, #cbd5e1);
            letter-spacing: 0.4rem;
            font-weight: 400;
        }

        .nd-token-input:focus {
            outline: none;
            border-color: var(--nd-gold-500);
            box-shadow: 0 0 0 4px rgba(212, 168, 75, 0.12);
        }

        /* Timer ring */
        .nd-timer-ring {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            background: var(--nd-surface-50, #f8fafc);
            border: 1px solid var(--nd-surface-200, #e2e8f0);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--nd-gray-500, #64748b);
        }

        .nd-timer-dot {
            width: 6px;
            height: 6px;
            background: var(--nd-gold-500);
            border-radius: 50%;
            animation: timerPulse 1s infinite;
        }

        @keyframes timerPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Security badges */
        .nd-security-badges {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            padding-top: 1.25rem;
            margin-top: 1.25rem;
            border-top: 1px solid var(--nd-surface-200, #e2e8f0);
        }

        .nd-security-badge {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.6875rem;
            font-weight: 500;
            color: var(--nd-gray-400, #94a3b8);
        }

        .nd-security-badge i {
            font-size: 0.75rem;
            color: var(--nd-gray-300, #cbd5e1);
        }
        
        /* Footer link */
        .nd-footer-link {
            color: var(--nd-gray-500, #64748b);
            font-size: 0.8125rem;
            text-decoration: none;
            transition: color 0.2s ease;
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
    
    <!-- 2FA Verification Card -->
    <div class="nd-login-card">
        <!-- Logo / Shield Icon -->
        <div class="nd-login-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L6.5 8.793l3.646-3.647z"/>
            </svg>
        </div>
        
        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="nd-login-title">Verificação de Segurança</h1>
            <p class="nd-login-subtitle">Informe o código de 6 dígitos gerado pelo seu aplicativo autenticador</p>
        </div>

        <!-- Error Alert -->
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
        
        <!-- 2FA Form -->
        <form method="POST" action="/admin/2fa/verify" id="twoFactorForm" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Token Input -->
            <div class="mb-3">
                <label for="code" class="nd-label">Código de Verificação</label>
                <input type="text" 
                       class="nd-token-input w-100" 
                       id="code" 
                       name="code" 
                       pattern="[0-9]{6}" 
                       maxlength="6" 
                       placeholder="000000" 
                       required 
                       autocomplete="one-time-code" 
                       inputmode="numeric"
                       autofocus>
            </div>
            
            <!-- Timer hint -->
            <div class="text-center mb-4">
                <span class="nd-timer-ring">
                    <span class="nd-timer-dot"></span>
                    O código se renova a cada 30 segundos
                </span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="nd-btn nd-btn-primary w-100 py-3 hover-translate-up" id="submitBtn">
                <span>VERIFICAR ACESSO</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L6.5 8.793l3.646-3.647z"/>
                </svg>
            </button>
        </form>
        
        <!-- Cancel / Back to Login -->
        <div class="text-center mt-3">
            <a href="/admin/login" class="nd-footer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" style="vertical-align: -0.1em;" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Voltar para o Login
            </a>
        </div>

        <!-- Security Badges (footer) -->
        <div class="nd-security-badges">
            <span class="nd-security-badge">
                <i class="bi bi-lock-fill"></i> Criptografado
            </span>
            <span class="nd-security-badge">
                <i class="bi bi-phone-fill"></i> TOTP
            </span>
            <span class="nd-security-badge">
                <i class="bi bi-shield-check"></i> RFC 6238
            </span>
        </div>
        
        <!-- Footer -->
        <p class="text-center mt-3 mb-0 x-small text-muted">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Acesso Restrito.
        </p>
    </div>

    <!-- Scripts (Local) -->
    <script src="<?= ($config['base_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
        const codeInput = document.getElementById('code');
        const submitBtn = document.getElementById('submitBtn');
        
        // Auto-format: only digits, max 6
        codeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            
            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                submitBtn.classList.add('pulse-once');
            }
        });

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
