<?php
/**
 * Portal Login - Design Premium Glassmorphism v2.0
 * BSI Capital Securitizadora
 * 
 * @var string $csrfToken
 * @var array $flash
 */
$error   = $flash['error']   ?? null;
$success = $flash['success'] ?? null;
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name'] ?? 'BSI Capital Securitizadora';
$primary  = $branding['primary_color'] ?? '#0c1b2e';
$accent   = $branding['accent_color']  ?? '#d4a84b';

// Generate color variants
if (class_exists(\App\Support\ColorUtils::class)) {
    $p900 = \App\Support\ColorUtils::adjustBrightness($primary, -15);
    $p800 = $primary;
    $g500 = $accent;
    $g600 = \App\Support\ColorUtils::adjustBrightness($accent, -15);
} else {
    $p900 = '#06101c'; $p800 = $primary;
    $g500 = $accent; $g600 = '#a67f3d';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal do Cliente - <?= htmlspecialchars($appName) ?>">
    <title>Portal do Cliente - <?= htmlspecialchars($appName) ?></title>
    
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
            --nd-gold-500: <?= $g500 ?>;
            --nd-gold-600: <?= $g600 ?>;
        }
        
        /* Portal specific background */
        .nd-portal-login {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, <?= $p900 ?> 0%, <?= $p800 ?> 40%, #1a2f4e 100%);
            position: relative;
            overflow: hidden;
        }
        
        /* Animated orbs */
        .nd-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: orbFloat 15s ease-in-out infinite;
        }
        
        .nd-orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 168, 75, 0.12) 0%, transparent 70%);
            top: -150px;
            right: -100px;
        }
        
        .nd-orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(45, 74, 115, 0.25) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation-delay: 5s;
        }
        
        .nd-orb-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(212, 168, 75, 0.08) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 10s;
        }
        
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0); opacity: 0.6; }
            25% { transform: translate(30px, -20px); opacity: 0.8; }
            50% { transform: translate(-20px, 30px); opacity: 0.5; }
            75% { transform: translate(20px, 10px); opacity: 0.7; }
        }
        
        /* Glass card styling */
        .nd-portal-card {
            width: calc(100% - 2rem);
            max-width: 480px;
            background: rgba(12, 27, 46, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            padding: 3rem;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            z-index: 10;
            animation: cardSlideIn 0.5s ease-out;
        }
        
        @keyframes cardSlideIn {
            0% { opacity: 0; transform: translateY(20px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        /* Logo container */
        .nd-portal-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.03) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .nd-portal-logo svg {
            width: 36px;
            height: 36px;
            color: var(--nd-gold-500);
        }
        
        /* Titles */
        .nd-portal-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        
        .nd-portal-subtitle {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        /* Code input field */
        .nd-code-label {
            display: block;
            text-align: center;
            color: var(--nd-gold-400);
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }
        
        .nd-code-input {
            width: 100%;
            height: 72px;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            color: #ffffff;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-align: center;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        
        .nd-code-input::placeholder {
            color: rgba(255, 255, 255, 0.25);
            letter-spacing: 0.2em;
        }
        
        .nd-code-input:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .nd-code-input:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.6);
            border-color: var(--nd-gold-500);
            box-shadow: 0 0 0 4px rgba(212, 168, 75, 0.15);
            transform: scale(1.01);
        }
        
        /* Submit button */
        .nd-portal-submit {
            width: 100%;
            height: 56px;
            margin-top: 2rem;
            background: linear-gradient(135deg, var(--nd-gold-500) 0%, var(--nd-gold-600) 100%);
            border: none;
            border-radius: 14px;
            color: #0c1b2e;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9375rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 20px rgba(212, 168, 75, 0.3);
        }
        
        .nd-portal-submit:hover {
            background: linear-gradient(135deg, #e4c47a 0%, var(--nd-gold-500) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 168, 75, 0.4);
        }
        
        .nd-portal-submit:active {
            transform: translateY(0);
        }
        
        /* Security footer */
        .nd-security-footer {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            text-align: center;
        }
        
        .nd-security-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 50px;
            margin-bottom: 1rem;
        }
        
        .nd-security-badge svg {
            color: var(--nd-gold-500);
            width: 14px;
            height: 14px;
        }
        
        .nd-security-badge span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .nd-security-text {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            line-height: 1.6;
            max-width: 280px;
            margin: 0 auto;
        }
        
        /* Alerts */
        .nd-portal-alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .nd-portal-alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        .nd-portal-alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
    </style>
</head>
<body class="nd-portal-login">
    
    <!-- Animated Orbs -->
    <div class="nd-orb nd-orb-1"></div>
    <div class="nd-orb nd-orb-2"></div>
    <div class="nd-orb nd-orb-3"></div>
    
    <!-- Portal Card -->
    <div class="nd-portal-card">
        <!-- Logo -->
        <div class="nd-portal-logo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.935.536.27.111.554.207.818.207.265 0 .548-.096.818-.207.283-.116.59-.292.935-.536a10.728 10.728 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453c-.386.273-.744.482-1.048.625-.28.132-.581.24-.829.24s-.548-.108-.829-.24a11.777 11.777 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        </div>
        
        <!-- Titles -->
        <h1 class="nd-portal-title">Portal do Cliente</h1>
        <p class="nd-portal-subtitle"><?= htmlspecialchars($appName) ?></p>
        
        <!-- Alerts -->
        <?php if ($error): ?>
            <div class="nd-portal-alert nd-portal-alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="nd-portal-alert nd-portal-alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form method="post" action="/portal/login" autocomplete="off">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <label for="access_code" class="nd-code-label">
                Código de Acesso
            </label>
            
            <input type="text"
                   class="nd-code-input"
                   id="access_code"
                   name="access_code"
                   placeholder="XXXX-XXXX-XXXX"
                   autocomplete="off"
                   required
                   autofocus
                   maxlength="14">
            
            <button type="submit" class="nd-portal-submit">
                <span>Acessar Portal</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
            </button>
        </form>
        
        <!-- Security Footer -->
        <div class="nd-security-footer">
            <div class="nd-security-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
                <span>Conexão Segura</span>
            </div>
            <p class="nd-security-text">
                Ambiente protegido por criptografia de ponta a ponta. Seus dados estão seguros.
            </p>
        </div>
    </div>

    <!-- Scripts (Local) -->
    <script src="<?= ($config['base_url'] ?? '') ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script>
        // Access Code Formatter - Real-time formatting
        const codeInput = document.getElementById('access_code');
        
        codeInput.addEventListener('input', function(e) {
            let input = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            let formatted = '';
            if (input.length > 0) formatted += input.substring(0, 4);
            if (input.length > 4) formatted += '-' + input.substring(4, 8);
            if (input.length > 8) formatted += '-' + input.substring(8, 12);
            
            e.target.value = formatted;
        });
        
        // Paste handler for full code
        codeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const clean = paste.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 12);
            
            let formatted = '';
            if (clean.length > 0) formatted += clean.substring(0, 4);
            if (clean.length > 4) formatted += '-' + clean.substring(4, 8);
            if (clean.length > 8) formatted += '-' + clean.substring(8, 12);
            
            this.value = formatted;
        });
    </script>
</body>
</html>