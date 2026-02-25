<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Auth;

use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Infrastructure\Security\DbRateLimiter;
use App\Support\AuditLogger;
use App\Support\Csrf;
use App\Support\Session;
use Respect\Validation\Validator as v;

final class LoginController
{
    private AuditLogger $audit;

    private DbRateLimiter $limiter;

    public function __construct(private array $config)
    {
        $this->audit = new AuditLogger($config['pdo']);
        $this->limiter = new DbRateLimiter($config['pdo']);
    }

    public function showLoginForm(array $vars = []): void
    {
        // Prepare view data with branding and config
        $viewData = [
            'branding' => $this->config['branding'] ?? [],
            'config' => $this->config,
            'errorMessage' => Session::getFlash('error'),
            'oldEmail' => Session::getFlash('old_email'),
            'csrfToken' => Csrf::token(),
        ];

        // Extract variables for the view
        extract($viewData);

        // Render standalone login view (no base layout with sidebar)
        require __DIR__ . '/../../../View/admin/auth/login.php';
    }

    public function handleLogin(array $vars = []): void
    {
        $post = $_POST ?? [];
        $email = trim($post['email'] ?? '');
        $password = (string) ($post['password'] ?? '');
        $token = $post['_token'] ?? '';

        // Rate limiting
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $scope = 'admin_login';
        if ($this->limiter->check($scope, $clientIp, 'ip_global', 5, 15)) {

            Session::flash('error', 'Muitas tentativas de login. Aguarde 15 minutos.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');

            return;
        }

        // CSRF
        if (!Csrf::validate($token)) {

            Session::flash('error', 'Sessão expirada. Tente novamente.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');

            return;
        }

        // Validação básica
        $emailIsValid = v::email()->length(1, 190)->validate($email);
        $passIsValid = v::stringType()->length(1, null)->validate($password);
        if (!$emailIsValid || !$passIsValid) {

            $this->limiter->increment($scope, $clientIp, 'ip_global', 5, 15);
            Session::flash('error', 'Credenciais inválidas.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');

            return;
        }

        // Repositório
        $pdo = $this->config['pdo'];
        $repo = new MySqlAdminUserRepository($pdo);

        $user = $repo->findActiveByEmail($email);
        if (!$user) {

            $this->limiter->increment($scope, $clientIp, 'ip_global', 5, 15);
            $this->audit->log('ADMIN', null, 'LOGIN_FAILED', 'ADMIN_USER', null);
            Session::flash('error', 'Credenciais inválidas.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');

            return;
        }

        // Checa se modo local permite senha
        if (!in_array($user['auth_mode'], ['LOCAL_ONLY', 'LOCAL_AND_MS'], true)) {

            $this->audit->log('ADMIN', (int) $user['id'], 'LOGIN_FAILED_AUTH_MODE', 'ADMIN_USER', (int) $user['id']);
            Session::flash('error', 'Esse usuário só pode entrar com Microsoft.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');

            return;
        }

        // Verifica hash
        if (empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {

            $this->limiter->increment($scope, $clientIp, 'ip_global', 5, 15);
            $this->audit->log('ADMIN', (int) $user['id'], 'LOGIN_FAILED', 'ADMIN_USER', (int) $user['id']);
            Session::flash('error', 'Credenciais inválidas.');
            Session::flash('old_email', $email);
            $this->redirect('/admin/login');

            return;
        }

        // OK: reset rate limiter
        $this->limiter->reset($scope, $clientIp, 'ip_global');

        // Check if 2FA is enabled
        if (!empty($user['two_factor_enabled']) && !empty($user['two_factor_secret'])) {
            // Store pending admin data for 2FA verification
            Session::put('2fa_pending_admin', [
                'id' => (int) $user['id'],
                'name' => $user['name'] ?? $user['full_name'] ?? '',
                'email' => $user['email'],
                'role' => $user['role'],
                'last_login_provider' => 'LOCAL',
            ]);

            $this->audit->log('ADMIN', (int) $user['id'], 'LOGIN_2FA_REQUIRED', 'ADMIN_USER', (int) $user['id']);
            $this->redirect('/admin/2fa/verify');

            return;
        }

        // No 2FA - complete login
        session_regenerate_id(true);

        $_SESSION['admin'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'] ?? $user['full_name'] ?? '',
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        $repo->updateLastLogin((int) $user['id'], 'LOCAL');
        $this->audit->log('ADMIN', (int) $user['id'], 'LOGIN_SUCCESS', 'ADMIN_USER', (int) $user['id']);

        // Force session write before redirect
        session_write_close();

        // Redireciona para dashboard

        $this->redirect('/admin');

        return;
    }

    private function redirect(string $path): void
    {
        session_write_close();
        header('Location: ' . $path);
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
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
