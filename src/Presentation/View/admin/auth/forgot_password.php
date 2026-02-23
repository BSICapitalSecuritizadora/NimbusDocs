<?php
/**
 * Forgot Password View
 * @var string $csrfToken
 * @var string|null $error
 * @var string|null $success
 */

$appName = $branding['app_name'] ?? 'NimbusDocs';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Credencial - <?= htmlspecialchars($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css" rel="stylesheet">
    <style>
        /* Login Page Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
        }
        
        .nd-login-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .nd-login-logo {
            animation: float 4s ease-in-out infinite;
        }
        
        /* Floating shapes */
        .nd-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
            background: var(--nd-gold-500);
        }
        
        .nd-shape-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            right: 10%;
            animation: float 6s ease-in-out infinite;
        }
        
        .nd-shape-2 {
            width: 200px;
            height: 200px;
            bottom: 20%;
            left: 5%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        .nd-shape-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 20%;
            animation: float 5s ease-in-out infinite;
        }
        
        /* Input group styling */
        .nd-input-group {
            position: relative;
        }
        
        .nd-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--nd-gray-400);
            transition: var(--nd-transition);
            z-index: 10;
        }
        
        .nd-input-group .nd-input {
            padding-left: 2.75rem;
        }
        
        .nd-input-group .nd-input:focus + .nd-input-icon,
        .nd-input-group .nd-input:focus ~ .nd-input-icon {
            color: var(--nd-gold-500);
        }
        
        /* Alert styling */
        .nd-alert {
            padding: 0.875rem 1rem;
            border-radius: var(--nd-radius);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.3s ease-out;
        }
        
        .nd-alert-danger {
            background: var(--nd-danger-light);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .nd-alert-danger i {
            color: var(--nd-danger);
        }

        .nd-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .nd-alert-success i {
            color: var(--nd-success);
        }
        
        .nd-alert-text {
            flex: 1;
            font-size: 0.875rem;
            color: var(--nd-gray-700);
        }
        
        .nd-alert-close {
            background: none;
            border: none;
            color: var(--nd-gray-400);
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
        
        .nd-alert-close:hover {
            color: var(--nd-gray-600);
        }
        
        /* Footer text */
        .nd-login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8125rem;
            color: var(--nd-gray-500);
        }
        
        /* Back link */
        .nd-back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .nd-back-link a {
            color: var(--nd-navy-600);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--nd-transition);
        }
        
        .nd-back-link a:hover {
            color: var(--nd-gold-600);
        }
    </style>
</head>
<body>
    <div class="nd-login-page">
        <!-- Floating Shapes -->
        <div class="nd-shape nd-shape-1"></div>
        <div class="nd-shape nd-shape-2"></div>
        <div class="nd-shape nd-shape-3"></div>
        
        <div class="nd-login-card">
            <!-- Logo -->
            <div class="nd-login-logo">
                <i class="bi bi-key-fill"></i>
            </div>
            
            <!-- Title -->
            <h1 class="nd-login-title">Recuperação de Credencial</h1>
            <p class="nd-login-subtitle">Informe seu e-mail corporativo para receber as instruções de redefinição de segurança.</p>
            
            <!-- Error Alert -->
            <?php if (!empty($error)): ?>
                <div class="nd-alert nd-alert-danger" id="alertError">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                    <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Success Alert -->
            <?php if (!empty($success)): ?>
                <div class="nd-alert nd-alert-success" id="alertSuccess">
                    <i class="bi bi-check-circle-fill"></i>
                    <span class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
                    <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Form -->
            <form method="POST" action="/admin/forgot-password">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                
                <div class="mb-4">
                    <label for="email" class="nd-label">E-mail Corporativo</label>
                    <div class="nd-input-group">
                        <input type="email" 
                               class="nd-input" 
                               id="email" 
                               name="email" 
                               placeholder="usuario@bsicapital.com.br"
                               required 
                               autofocus>
                        <i class="bi bi-envelope nd-input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="nd-btn nd-btn-gold nd-btn-lg" style="width: 100%;">
                    <i class="bi bi-send-fill me-2"></i>
                    Enviar Instruções
                </button>
            </form>
            
            <div class="nd-back-link">
                <a href="/admin/login">
                    <i class="bi bi-arrow-left"></i>
                    Retornar ao Acesso
                </a>
            </div>
            
            <p class="nd-login-footer">
                Ambiente seguro e monitorado. Acesso restrito.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
