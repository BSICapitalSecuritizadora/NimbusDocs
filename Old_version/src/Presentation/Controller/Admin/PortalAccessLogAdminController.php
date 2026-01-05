<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalAccessLogRepository;
use App\Support\Auth;

final class PortalAccessLogAdminController
{
    private MySqlPortalAccessLogRepository $repo;

    public function __construct(private array $config)
    {
        $this->repo = new MySqlPortalAccessLogRepository($config['pdo']);
    }

    public function index(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $filters = [
            'email'        => $_GET['email']         ?? '',
            'action'       => $_GET['action']        ?? '',
            'resource_type' => $_GET['resource_type'] ?? '',
            'from_date'    => $_GET['from_date']     ?? '',
            'to_date'      => $_GET['to_date']       ?? '',
        ];

        $logs = $this->repo->search($filters);

        $pageTitle   = 'Log de acessos do portal';
        $contentView = __DIR__ . '/../../View/admin/access_log/portal.php';

        $viewData = [
            'filters' => $filters,
            'logs'    => $logs,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }
}
