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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
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
        
        /* Microsoft button */
        .nd-btn-microsoft {
            background: var(--nd-white);
            border: 1px solid var(--nd-gray-300);
            color: var(--nd-gray-700);
            width: 100%;
        }
        
        .nd-btn-microsoft:hover {
            background: var(--nd-gray-100);
            border-color: var(--nd-gray-400);
            color: var(--nd-gray-800);
        }
        
        .nd-btn-microsoft i {
            color: #00a4ef;
        }
        
        /* Footer text */
        .nd-login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8125rem;
            color: var(--nd-gray-500);
        }
        
        /* Forgot password link */
        .nd-forgot-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .nd-forgot-link a {
            color: var(--nd-navy-600);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--nd-transition);
        }
        
        .nd-forgot-link a:hover {
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
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            
            <!-- Title -->
            <h1 class="nd-login-title"><?= htmlspecialchars($appName) ?></h1>
            <p class="nd-login-subtitle">Portal Corporativo</p>
            
            <!-- Error Alert -->
            <?php $error = $errorMessage ?? \App\Support\Session::getFlash('error'); ?>
            <?php if (!empty($error)): ?>
                <div class="nd-alert nd-alert-danger" id="alertError">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
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
                    <div class="nd-input-group">
                        <input type="email" 
                               class="nd-input" 
                               id="email" 
                               name="email" 
                               placeholder="usuario@bsicapital.com.br"
                               value="<?= htmlspecialchars($oldEmail ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               required 
                               autofocus>
                        <i class="bi bi-envelope nd-input-icon"></i>
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
                        <i class="bi bi-key nd-input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="nd-btn nd-btn-gold nd-btn-lg" style="width: 100%;">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Acessar Plataforma
                </button>
            </form>
            
            <div class="nd-forgot-link">
                <a href="/admin/forgot-password">Recuperar Credenciais</a>
            </div>
            
            <div class="d-flex align-items-center my-4">
                <div class="flex-grow-1 border-top border-secondary-subtle"></div>
                <span class="px-3 text-muted x-small fw-semibold text-uppercase">ou</span>
                <div class="flex-grow-1 border-top border-secondary-subtle"></div>
            </div>
            
            <a href="/admin/login/microsoft" class="btn btn-white w-100 py-2-5 border border-subtle d-flex align-items-center justify-content-center gap-2 shadow-sm nd-btn-microsoft hover-lift">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" alt="Microsoft" style="width: 18px; height: 18px;">
                <span class="fw-medium text-dark" style="font-size: 0.9rem;">Entrar com Microsoft</span>
            </a>
            
            <p class="nd-login-footer">
                Ambiente seguro e monitorado. Acesso restrito.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>