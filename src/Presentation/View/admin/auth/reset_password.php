<?php
/**
 * Reset Password View
 * @var string $csrfToken
 * @var string $token
 * @var array $resetData
 * @var string|null $error
 */

$appName = $branding['app_name'] ?? 'NimbusDocs';
$primaryColor = $branding['primary_color'] ?? '#00205b';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - <?= htmlspecialchars($appName) ?></title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ($config['asset_url'] ?? $config['base_url'] ?? '') ?>/css/nimbusdocs-theme.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?= htmlspecialchars($primaryColor) ?>;
        }
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #001a4d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 420px;
            width: 100%;
            padding: 2.5rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #6c757d;
            font-size: 0.95rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 32, 91, 0.15);
        }
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #001a4d;
            border-color: #001a4d;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #001a4d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrapper i {
            font-size: 2rem;
            color: white;
        }
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        .password-requirements li {
            margin-bottom: 0.25rem;
        }
        .password-requirements li.valid {
            color: #198754;
        }
        .password-requirements li.valid::before {
            content: "✓ ";
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="icon-wrapper">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1>Definição de Nova Senha</h1>
            <p>Olá, <strong><?= htmlspecialchars($resetData['name'] ?? '') ?></strong>! Por favor, defina uma nova credencial segura.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/reset-password" id="resetForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Nova Senha Segura</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Mínimo 8 caracteres" required minlength="8">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <ul class="password-requirements mt-2 ps-3">
                    <li id="req-length">Pelo menos 8 caracteres</li>
                    <li id="req-upper">Caractere maiúsculo</li>
                    <li id="req-lower">Caractere minúsculo</li>
                    <li id="req-number">Pelo menos um número</li>
                </ul>
            </div>

            <div class="mb-4">
                <label for="password_confirm" class="form-label">Confirmar Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                           placeholder="Repita a nova senha para confirmação" required>
                </div>
                <div id="password-match" class="form-text"></div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-check-lg me-2"></i>Atualizar Credencial
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Password validation
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirm');
        const reqLength = document.getElementById('req-length');
        const reqUpper = document.getElementById('req-upper');
        const reqLower = document.getElementById('req-lower');
        const reqNumber = document.getElementById('req-number');
        const passwordMatch = document.getElementById('password-match');

        password.addEventListener('input', validatePassword);
        passwordConfirm.addEventListener('input', checkMatch);

        function validatePassword() {
            const val = password.value;
            
            reqLength.classList.toggle('valid', val.length >= 8);
            reqUpper.classList.toggle('valid', /[A-Z]/.test(val));
            reqLower.classList.toggle('valid', /[a-z]/.test(val));
            reqNumber.classList.toggle('valid', /[0-9]/.test(val));
            
            checkMatch();
        }

        function checkMatch() {
            if (passwordConfirm.value === '') {
                passwordMatch.textContent = '';
                return;
            }
            
            if (password.value === passwordConfirm.value) {
                passwordMatch.textContent = '✓ Confirmação válida';
                passwordMatch.className = 'form-text text-success';
            } else {
                passwordMatch.textContent = '✗ As senhas divergem';
                passwordMatch.className = 'form-text text-danger';
            }
        }
    </script>
</body>
</html>
