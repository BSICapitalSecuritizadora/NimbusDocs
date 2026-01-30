<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Auth\TwoFactorAuthService;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\Csrf;
use App\Support\Session;

/**
 * Controller for Two-Factor Authentication management
 */
class TwoFactorController
{
    private array $config;
    private TwoFactorAuthService $twoFactorService;
    private MySqlAdminUserRepository $userRepo;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->twoFactorService = new TwoFactorAuthService();
        $this->userRepo = new MySqlAdminUserRepository($config['pdo']);
    }

    /**
     * Show 2FA setup page with QR code
     */
    public function showSetup(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        $user = $this->userRepo->findById((int) $admin['id']);
        
        // Generate new secret if not exists or not enabled
        $secret = $user['two_factor_secret'] ?? null;
        if (!$secret || !($user['two_factor_enabled'] ?? false)) {
            $secret = $this->twoFactorService->generateSecret();
            $this->userRepo->update2FA((int) $admin['id'], $secret, false);
        }

        $qrCodeUrl = $this->twoFactorService->getQrCodeUrl(
            $secret,
            $user['email'],
            $this->config['branding']['app_name'] ?? 'NimbusDocs'
        );

        $csrfToken = Csrf::token();
        $error = Session::getFlash('error');
        $success = Session::getFlash('success');
        $branding = $this->config['branding'] ?? [];
        $isEnabled = (bool) ($user['two_factor_enabled'] ?? false);

        $isEnabled = (bool) ($user['two_factor_enabled'] ?? false);

        // Standard Layout Pattern
        $pageTitle = 'Autenticação em Dois Fatores';
        $contentView = __DIR__ . '/../../View/admin/settings/two_factor.php';
        
        $otpAuthUrl = $this->twoFactorService->getOtpAuthUrl(
            $secret,
            $user['email'],
            $this->config['branding']['app_name'] ?? 'NimbusDocs'
        );

        $viewData = [
            'admin'     => $admin,
            'csrfToken' => $csrfToken,
            'qrCodeUrl' => $qrCodeUrl,
            'otpAuthUrl' => $otpAuthUrl,
            'secret'    => $secret,
            'isEnabled' => $isEnabled,
            'branding'  => $branding,
            'error'     => $error,
            'success'   => $success
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    /**
     * Enable 2FA after verifying code
     */
    public function enable(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de segurança inválido.');
            header('Location: /admin/2fa/setup');
            exit;
        }

        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        $code = trim($_POST['code'] ?? '');
        $user = $this->userRepo->findById((int) $admin['id']);
        $secret = $user['two_factor_secret'] ?? '';

        if (!$this->twoFactorService->verify($secret, $code)) {
            Session::flash('error', 'Código inválido. Verifique se o relógio do seu dispositivo está sincronizado.');
            header('Location: /admin/2fa/setup');
            exit;
        }

        // Enable 2FA
        $this->userRepo->update2FA((int) $admin['id'], $secret, true);
        $this->userRepo->confirm2FA((int) $admin['id']);

        // Log the action
        if (isset($this->config['audit'])) {
            $this->config['audit']->log(
                'ADMIN',
                (int) $admin['id'],
                '2FA_ENABLED',
                'admin_user',
                (int) $admin['id'],
                []
            );
        }

        Session::flash('success', 'Autenticação em dois fatores ativada com sucesso!');
        header('Location: /admin/2fa/setup');
        exit;
    }

    /**
     * Disable 2FA
     */
    public function disable(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de segurança inválido.');
            header('Location: /admin/2fa/setup');
            exit;
        }

        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        $code = trim($_POST['code'] ?? '');
        $user = $this->userRepo->findById((int) $admin['id']);
        $secret = $user['two_factor_secret'] ?? '';

        if (!$this->twoFactorService->verify($secret, $code)) {
            Session::flash('error', 'Código inválido. Digite o código atual do seu aplicativo autenticador.');
            header('Location: /admin/2fa/setup');
            exit;
        }

        // Disable 2FA
        $this->userRepo->update2FA((int) $admin['id'], null, false);

        // Log the action
        if (isset($this->config['audit'])) {
            $this->config['audit']->log(
                'ADMIN',
                (int) $admin['id'],
                '2FA_DISABLED',
                'admin_user',
                (int) $admin['id'],
                []
            );
        }

        Session::flash('success', 'Autenticação em dois fatores desativada.');
        header('Location: /admin/2fa/setup');
        exit;
    }

    /**
     * Show 2FA verification page (during login)
     */
    public function showVerify(): void
    {
        $pendingAdmin = Session::get('2fa_pending_admin');
        if (!$pendingAdmin) {
            header('Location: /admin/login');
            exit;
        }

        $viewData = [
            'branding'  => $this->config['branding'] ?? [],
            'config'    => $this->config,
            'csrfToken' => Csrf::token(),
            'error'     => Session::getFlash('error'),
        ];
        extract($viewData);

        require __DIR__ . '/../../View/admin/auth/two_factor_verify.php';
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Token de segurança inválido.');
            header('Location: /admin/2fa/verify');
            exit;
        }

        $pendingAdmin = Session::get('2fa_pending_admin');
        if (!$pendingAdmin) {
            header('Location: /admin/login');
            exit;
        }

        $code = trim($_POST['code'] ?? '');
        $user = $this->userRepo->findById((int) $pendingAdmin['id']);
        $secret = $user['two_factor_secret'] ?? '';

        if (!$this->twoFactorService->verify($secret, $code)) {
            Session::flash('error', 'Código inválido. Tente novamente.');
            header('Location: /admin/2fa/verify');
            exit;
        }

        // 2FA verified - complete login
        Session::forget('2fa_pending_admin');
        Session::put('admin', $pendingAdmin);

        // Update last login
        $this->userRepo->updateLastLogin((int) $pendingAdmin['id'], $pendingAdmin['last_login_provider'] ?? 'LOCAL');

        // Log the action
        if (isset($this->config['audit'])) {
            $this->config['audit']->log(
                'ADMIN',
                (int) $pendingAdmin['id'],
                '2FA_VERIFIED',
                'admin_user',
                (int) $pendingAdmin['id'],
                []
            );
        }

        header('Location: /admin/dashboard');
        exit;
    }
}
