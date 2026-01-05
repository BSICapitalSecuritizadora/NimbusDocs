<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Session;

final class NotificationOutboxAdminController
{
    private MySqlNotificationOutboxRepository $outbox;

    public function __construct(private array $config)
    {
        $logger = $config['logger'] ?? null;
        $this->outbox = new MySqlNotificationOutboxRepository($config['pdo'], $logger);
    }

    public function index(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $defaultFrom = (new \DateTimeImmutable('-30 days'))->format('Y-m-d');
        $defaultTo   = (new \DateTimeImmutable('today'))->format('Y-m-d');

        $filters = [
            'status'    => $_GET['status']    ?? '',
            'type'      => $_GET['type']      ?? '',
            'email'     => $_GET['email']     ?? '',
            'from_date' => $_GET['from_date'] ?? $defaultFrom,
            'to_date'   => $_GET['to_date']   ?? $defaultTo,
        ];

        $rows     = $this->outbox->search($filters, 200);
        $types    = $this->outbox->distinctTypes();
        $statuses = $this->outbox->distinctStatuses();

        $pageTitle   = 'Fila de notificações (Outbox)';
        $contentView = __DIR__ . '/../../View/admin/notifications/outbox/index.php';

        $viewData = [
            'filters'  => $filters,
            'rows'     => $rows,
            'types'    => $types,
            'statuses' => $statuses,
            'csrfToken' => Csrf::token(),
            'success'  => Session::getFlash('success'),
            'error'    => Session::getFlash('error'),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function show(array $vars): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $id  = (int)($vars['id'] ?? 0);
        $row = $this->outbox->find($id);

        if (!$row) {
            Session::flash('error', 'Notificação não encontrada.');
            header('Location: /admin/notifications/outbox');
            exit;
        }

        $payload = null;
        try {
            $payload = json_decode((string)$row['payload_json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $payload = ['_error' => 'Payload inválido: ' . $e->getMessage()];
        }

        $pageTitle   = 'Detalhes da notificação';
        $contentView = __DIR__ . '/../../View/admin/notifications/outbox/show.php';

        $viewData = [
            'row'      => $row,
            'payload'  => $payload,
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
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

        if ($this->outbox->cancel($id)) {
            Session::flash('success', 'Notificação cancelada (PENDING → CANCELLED).');
        } else {
            Session::flash('error', 'Não foi possível cancelar. (Apenas PENDING pode ser cancelada)');
        }

        header('Location: /admin/notifications/outbox');
        exit;
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

        if ($this->outbox->reprocess($id)) {
            Session::flash('success', 'Notificação marcada para reprocessamento (FAILED → PENDING).');
        } else {
            Session::flash('error', 'Não foi possível reprocessar. (Apenas FAILED pode reprocessar)');
        }

        header('Location: /admin/notifications/outbox');
        exit;
    }

    public function resetAndReprocess(array $vars): void
    {
        Auth::requireRole('SUPER_ADMIN');

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Sessão expirada.');
            header('Location: /admin/notifications/outbox');
            exit;
        }

        $id = (int)($vars['id'] ?? 0);

        if ($this->outbox->resetAttemptsAndReprocess($id)) {
            Session::flash('success', 'Notificação resetada e reprocessada (attempts=0, status=PENDING).');
        } else {
            Session::flash('error', 'Não foi possível resetar/reprocessar.');
        }

        header('Location: /admin/notifications/outbox');
        exit;
    }
}
