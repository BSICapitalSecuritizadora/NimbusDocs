<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Auth;

use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\AuditLogger;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\RateLimiter;
use Respect\Validation\Validator as v;

final class LoginController
{
    private AuditLogger $audit;

    public function __construct(private array $config)
    {
        $this->audit = new AuditLogger($config['pdo']);
    }

    public function showLoginForm(array $vars = []): void
    {
        // Prepare view data with branding and config
        $viewData = [
            'branding'     => $this->config['branding'] ?? [],
            'config'       => $this->config,
            'errorMessage' => Session::getFlash('error'),
            'oldEmail'     => Session::getFlash('old_email'),
            'csrfToken'    => Csrf::token(),
        ];

        // Extract variables for the view
        extract($viewData);

        // Render standalone login view (no base layout with sidebar)
        require __DIR__ . '/../../../View/admin/auth/login.php';
    }

    public function handleLogin(array $vars = []): void
    {
        $post = $_POST ?? [];
        $email    = trim($post['email'] ?? '');
        $password = (string)($post['password'] ?? '');
        $token    = $post['_token'] ?? '';

        // Rate limiting
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!RateLimiter::isAllowed($clientIp, 5, 900)) {
            $remaining = RateLimiter::getTimeRemaining($clientIp);
            Session::flash('error', 'Muitas tentativas de login. Tente novamente em ' . ceil($remaining / 60) . ' minutos.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');
        }

        // CSRF
        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');
        }

        // Validação básica
        $emailIsValid = v::email()->length(1, 190)->validate($email);
        $passIsValid  = v::stringType()->length(1, null)->validate($password);
        if (!$emailIsValid || !$passIsValid) {
            RateLimiter::recordAttempt($clientIp, 900);
            Session::flash('error', 'Credenciais inválidas.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');
        }

        // Repositório
        $pdo = $this->config['pdo'];
        $repo = new MySqlAdminUserRepository($pdo);

        $user = $repo->findActiveByEmail($email);
        if (!$user) {
            RateLimiter::recordAttempt($clientIp, 900);
            $this->audit->log('ADMIN', null, 'LOGIN_FAILED', 'ADMIN_USER', null);
            Session::flash('error', 'Credenciais inválidas.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');
        }

        // Checa se modo local permite senha
        if (!in_array($user['auth_mode'], ['LOCAL_ONLY', 'LOCAL_AND_MS'], true)) {
            $this->audit->log('ADMIN', (int)$user['id'], 'LOGIN_FAILED_AUTH_MODE', 'ADMIN_USER', (int)$user['id']);
            Session::flash('error', 'Esse usuário só pode entrar com Microsoft.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');
        }

        // Verifica hash
        if (empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            RateLimiter::recordAttempt($clientIp, 900);
            $this->audit->log('ADMIN', (int)$user['id'], 'LOGIN_FAILED', 'ADMIN_USER', (int)$user['id']);
            Session::flash('error', 'Credenciais inválidas.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');
        }

        // OK: reset rate limiter
        RateLimiter::reset($clientIp);

        // Check if 2FA is enabled
        if (!empty($user['two_factor_enabled']) && !empty($user['two_factor_secret'])) {
            // Store pending admin data for 2FA verification
            Session::put('2fa_pending_admin', [
                'id'    => (int)$user['id'],
                'name'  => $user['name'] ?? $user['full_name'] ?? '',
                'email' => $user['email'],
                'role'  => $user['role'],
                'last_login_provider' => 'LOCAL',
            ]);
            
            $this->audit->log('ADMIN', (int)$user['id'], 'LOGIN_2FA_REQUIRED', 'ADMIN_USER', (int)$user['id']);
            $this->redirect('/admin/2fa/verify');
        }

        // No 2FA - complete login
        // Set session data directly
        $_SESSION['admin'] = [
            'id'    => (int)$user['id'],
            'name'  => $user['name'] ?? $user['full_name'] ?? '',
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        $repo->updateLastLogin((int)$user['id'], 'LOCAL');
        $this->audit->log('ADMIN', (int)$user['id'], 'LOGIN_SUCCESS', 'ADMIN_USER', (int)$user['id']);

        // Force session write before redirect
        session_write_close();

        // Redireciona para dashboard
        header('Location: /admin');
        exit;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public function logout(array $vars = []): void
    {
        Session::forget('admin');
        session_regenerate_id(true);

        Session::flash('error', null);      // limpa msg, se quiser
        Session::flash('old_email', null);  // limpa email, se quiser

        header('Location: /admin/login');
        exit;
    }
}
