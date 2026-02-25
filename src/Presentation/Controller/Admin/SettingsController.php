<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlSettingsRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Session;

final class SettingsController
{
    private MySqlSettingsRepository $settingsRepo;

    public function __construct(private array $config)
    {
        $this->settingsRepo = $config['settings_repo'];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public function index(): void
    {
        // só super admin mexe em config global
        $admin = Auth::requireRole('SUPER_ADMIN');

        $pageTitle = 'Configurações';
        // para simplificar, redireciona direto para notificações
        $this->redirect('/admin/settings/notifications');
    }

    public function notificationsForm(): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN');

        $settings = $this->settingsRepo->getAll();

        $pageTitle = 'Configurações de notificações';
        $contentView = __DIR__ . '/../../View/admin/settings/notifications.php';

        $viewData = [
            'admin' => $admin,
            'settings' => $settings,
            'csrfToken' => Csrf::token(),
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function saveNotifications(): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN');

        $post = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/settings/notifications');
        }

        $data = [
            'portal.notify.new_submission' => isset($post['portal_notify_new_submission']) ? '1' : '0',
            'portal.notify.status_change' => isset($post['portal_notify_status_change']) ? '1' : '0',
            'portal.notify.response_upload' => isset($post['portal_notify_response_upload']) ? '1' : '0',
            'portal.notify.access_link' => isset($post['portal_notify_access_link']) ? '1' : '0',
        ];

        $this->settingsRepo->setMany($data);

        Session::flash('success', 'Configurações de notificações atualizadas com sucesso.');

        $this->redirect('/admin/settings/notifications');
    }

    public function brandingForm(): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN');

        $settings = $this->settingsRepo->getAll();

        $branding = [
            'app_name' => $settings['app.name'] ?? 'NimbusDocs',
            'app_subtitle' => $settings['app.subtitle'] ?? 'Portal de documentos',
            'primary_color' => $settings['branding.primary_color'] ?? '#00205b',
            'accent_color' => $settings['branding.accent_color'] ?? '#ffc20e',
            'admin_logo_url' => $settings['branding.admin_logo_url'] ?? '',
            'portal_logo_url' => $settings['branding.portal_logo_url'] ?? '',
        ];

        $pageTitle = 'Branding e identidade visual';
        $contentView = __DIR__ . '/../../View/admin/settings/branding.php';

        $viewData = [
            'admin' => $admin,
            'branding' => $branding,
            'csrfToken' => Csrf::token(),
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function saveBranding(): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN');

        $post = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/settings/branding');
        }

        $appName = trim($post['app_name'] ?? 'NimbusDocs');
        $appSubtitle = trim($post['app_subtitle'] ?? 'Portal de documentos');

        $primary = trim($post['primary_color'] ?? '#00205b');
        $accent = trim($post['accent_color'] ?? '#ffc20e');

        $adminLogo = trim($post['admin_logo_url'] ?? '');
        $portalLogo = trim($post['portal_logo_url'] ?? '');

        $data = [
            'app.name' => $appName,
            'app.subtitle' => $appSubtitle,
            'branding.primary_color' => $primary,
            'branding.accent_color' => $accent,
            'branding.admin_logo_url' => $adminLogo,
            'branding.portal_logo_url' => $portalLogo,
        ];

        $this->settingsRepo->setMany($data);

        Session::flash('success', 'Configurações de branding atualizadas com sucesso.');
        $this->redirect('/admin/settings/branding');
    }
}
