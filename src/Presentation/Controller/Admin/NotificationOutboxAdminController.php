<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;
use App\Support\Auth;
use App\Support\Session;
use App\Support\Csrf;

final class NotificationOutboxAdminController
{
    private MySqlNotificationOutboxRepository $outbox;

    public function __construct(private array $config)
    {
        $this->outbox = new MySqlNotificationOutboxRepository($config['pdo']);
    }

    public function index(): void
    {
        $admin = Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $filters = [
            'status'    => trim($_GET['status'] ?? ''),
            'recipient' => trim($_GET['recipient'] ?? ''),
            'type'      => trim($_GET['type'] ?? ''),
        ];

        $items = $this->outbox->list($filters, 200);

        $pageTitle   = 'Fila de notificações';
        $contentView = __DIR__ . '/../../View/admin/notifications_outbox/index.php';

        $viewData = [
            'admin'     => $admin,
            'items'     => $items,
            'filters'   => $filters,
            'csrfToken' => Csrf::token(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
            'branding'  => $this->config['branding'] ?? [],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function reprocess(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/notifications/outbox');
            exit;
        }

        $id = (int)($vars['id'] ?? 0);
        $this->outbox->reprocess($id);

        Session::flash('success', 'Notificação reenfileirada.');
        header('Location: /admin/notifications/outbox');
        exit;
    }

    public function cancel(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/notifications/outbox');
            exit;
        }

        $id = (int)($vars['id'] ?? 0);
        $this->outbox->cancel($id);

        Session::flash('success', 'Notificação cancelada.');
        header('Location: /admin/notifications/outbox');
        exit;
    }
}
