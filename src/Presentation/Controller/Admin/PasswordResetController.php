<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPasswordResetRepository;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\PasswordValidator;
use App\Infrastructure\Security\DbRateLimiter;

/**
 * Controller for admin password recovery
 */
class PasswordResetController
{
    private array $config;
    private MySqlPasswordResetRepository $resetRepo;
    private MySqlAdminUserRepository $userRepo;
    private DbRateLimiter $limiter;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->resetRepo = new MySqlPasswordResetRepository($config['pdo']);
        $this->userRepo = new MySqlAdminUserRepository($config['pdo']);
        $this->limiter = new DbRateLimiter($config['pdo']);
    }

    /**
     * Show forgot password form
     */
    public function showForgotForm(): void
    {
        $viewData = [
            'branding'  => $this->config['branding'] ?? [],
            'config'    => $this->config,
            'csrfToken' => Csrf::token(),
            'error'     => Session::getFlash('error'),
            'success'   => Session::getFlash('success'),
        ];
        extract($viewData);

        require __DIR__ . '/../../View/admin/auth/forgot_password.php';
    }

    /**
     * Process forgot password request - send reset link
     */
    public function sendResetLink(): void
    {
        // CSRF validation
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de segurança inválido.');
            header('Location: /admin/forgot-password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        // Rate limiting - 3 attempts per 15 minutes per IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $scope = 'password_reset';
        
        if ($this->limiter->check($scope, $ip, 'ip_global', 3, 15)) {
            Session::flash('error', 'Muitas tentativas. Aguarde alguns minutos.');
            header('Location: /admin/forgot-password');
            exit;
        }

        $this->limiter->increment($scope, $ip, 'ip_global', 3, 15);

        // Always show success message (don't reveal if email exists)
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('success', 'Se o e-mail existir em nossa base, você receberá um link de recuperação.');
            header('Location: /admin/forgot-password');
            exit;
        }

        // Find user
        $user = $this->userRepo->findByEmail($email);

        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiresAt = new \DateTime('+1 hour');

            // Create new token (handling revocation internally)
            $this->resetRepo->create((int) $user['id'], $token, $expiresAt);

            // Send email
            $this->sendResetEmail($user['email'], $user['name'] ?? $user['full_name'] ?? 'Usuário', $token);

            // Log the request
            if (isset($this->config['audit'])) {
                $this->config['audit']->log(
                    'ADMIN',
                    (int) $user['id'],
                    'PASSWORD_RESET_REQUESTED',
                    'admin_user',
                    (int) $user['id'],
                    ['email' => $email]
                );
            }
        }

        Session::flash('success', 'Se o e-mail existir em nossa base, você receberá um link de recuperação.');
        header('Location: /admin/forgot-password');
        exit;
    }

    /**
     * Show reset password form
     */
    public function showResetForm(array $params): void
    {
        $token = $params['token'] ?? '';

        // Validate token
        $resetData = $this->resetRepo->findValidByToken($token);

        if (!$resetData) {
            Session::flash('error', 'Link de recuperação inválido ou expirado.');
            header('Location: /admin/forgot-password');
            exit;
        }

        $viewData = [
            'branding'  => $this->config['branding'] ?? [],
            'config'    => $this->config,
            'csrfToken' => Csrf::token(),
            'error'     => Session::getFlash('error'),
            'token'     => $token,
        ];
        extract($viewData);

        require __DIR__ . '/../../View/admin/auth/reset_password.php';
    }

    /**
     * Process password reset
     */
    public function resetPassword(): void
    {
        // CSRF validation
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de segurança inválido.');
            header('Location: /admin/forgot-password');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validate token
        $resetData = $this->resetRepo->findValidByToken($token);

        if (!$resetData) {
            Session::flash('error', 'Link de recuperação inválido ou expirado.');
            header('Location: /admin/forgot-password');
            exit;
        }

        // Validate password
        if (strlen($password) < 8) {
            Session::flash('error', 'A senha deve ter pelo menos 8 caracteres.');
            header("Location: /admin/reset-password/{$token}");
            exit;
        }

        if ($password !== $passwordConfirm) {
            Session::flash('error', 'As senhas não coincidem.');
            header("Location: /admin/reset-password/{$token}");
            exit;
        }

        // Password strength validation
        $passwordErrors = PasswordValidator::validate($password);
        if (!empty($passwordErrors)) {
            Session::flash('error', implode(' ', $passwordErrors));
            header("Location: /admin/reset-password/{$token}");
            exit;
        }

        // Update password
        $userId = (int) $resetData['admin_user_id'];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->userRepo->updatePassword($userId, $passwordHash);

        // Mark token as used
        $this->resetRepo->markAsUsed($token);

        // Delete all tokens for this user
        $this->resetRepo->deleteByUserId($userId);

        // Log the action
        if (isset($this->config['audit'])) {
            $this->config['audit']->log(
                'ADMIN',
                $userId,
                'PASSWORD_RESET_COMPLETED',
                'admin_user',
                $userId,
                []
            );
        }

        Session::flash('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
        header('Location: /admin/login');
        exit;
    }

    /**
     * Send password reset email
     */
    private function sendResetEmail(string $email, string $name, string $token): void
    {
        $mail = $this->config['mail'] ?? null;

        if (!$mail) {
            return;
        }

        $appUrl = rtrim($this->config['app']['url'] ?? 'http://localhost', '/');
        $resetUrl = "{$appUrl}/admin/reset-password/{$token}";
        $appName = $this->config['branding']['app_name'] ?? 'NimbusDocs';

        // Render email template
        ob_start();
        require __DIR__ . '/../../Email/password_reset.php';
        $htmlBody = ob_get_clean();

        try {
            $mail->send(
                $email,
                "Recuperação de Senha - {$appName}",
                $htmlBody
            );
        } catch (\Throwable $e) {
            // Log error but don't fail
            if (isset($this->config['logger'])) {
                $this->config['logger']->error('Failed to send password reset email', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

