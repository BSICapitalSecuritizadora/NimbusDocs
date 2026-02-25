<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Domain\Repository\AuditLogRepository;
use App\Infrastructure\Persistence\MySqlAuditLogRepository;
use App\Support\Session;

final class AuditLogAdminController
{
    private AuditLogRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlAuditLogRepository($config['pdo']);
    }

    private function requireAdmin(): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 50;

        $filters = [
            'actor_type' => $_GET['actor_type'] ?? null,
            'action' => $_GET['action'] ?? null,
            'context_type' => $_GET['context_type'] ?? null,
            'search' => $_GET['search'] ?? null,
        ];

        $result = $this->repo->paginate($page, $perPage, $filters);

        $pageTitle = 'Auditoria do sistema';
        $contentView = __DIR__ . '/../../View/admin/audit/index.php';
        $viewData = [
            'logs' => $result['items'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
            'filters' => $filters,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }
}
