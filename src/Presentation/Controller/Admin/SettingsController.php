<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlSettingsRepository;
use App\Support\Session;
use App\Support\Csrf;
use App\Support\Auth;

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

        $pageTitle   = 'Configurações';
        // para simplificar, redireciona direto para notificações
        $this->redirect('/admin/settings/notifications');
    }

    public function notificationsForm(): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN');

        $settings = $this->settingsRepo->getAll();

        $pageTitle   = 'Configurações de notificações';
        $contentView = __DIR__ . '/../../View/admin/settings/notifications.php';

        $viewData = [
            'admin'    => $admin,
            'settings' => $settings,
            'csrfToken' => Csrf::token(),
            'success'  => Session::flash('success'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function saveNotifications(): void
    {
        $admin = Auth::requireRole('SUPER_ADMIN');

        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/settings/notifications');
        }

        $data = [
            'portal.notify.new_submission'  => isset($post['portal_notify_new_submission'])  ? '1' : '0',
            'portal.notify.status_change'   => isset($post['portal_notify_status_change'])   ? '1' : '0',
            'portal.notify.response_upload' => isset($post['portal_notify_response_upload']) ? '1' : '0',
        ];

        $this->settingsRepo->setMany($data);

        Session::flash('success', 'Configurações de notificações atualizadas com sucesso.');

        $this->redirect('/admin/settings/notifications');
    }
}
