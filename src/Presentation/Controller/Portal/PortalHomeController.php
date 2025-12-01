<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Support\Session;

final class PortalHomeController
{
    private MySqlPortalSubmissionRepository $submissionRepo;

    public function __construct(private array $config)
    {
        $this->submissionRepo = new MySqlPortalSubmissionRepository($config['pdo']);
    }

    private function requireUser(): array
    {
        $user = Session::get('portal_user');
        if (!$user) {
            header('Location: /portal/login');
            exit;
        }
        return $user;
    }

    public function index(array $vars = []): void
    {
        $user = $this->requireUser();
        $userId = (int)$user['id'];

        // KPIs do usuário
        $total       = $this->submissionRepo->countForUser($userId);
        $pendentes   = $this->submissionRepo->countForUserByStatus($userId, 'PENDENTE');
        $concluidas  = $this->submissionRepo->countForUserByStatus($userId, 'FINALIZADA');

        // Últimas submissões
        $submissions = $this->submissionRepo->latestForUser($userId, 10);

        $pageTitle   = 'Minhas informações';
        $contentView = __DIR__ . '/../../View/portal/dashboard/index.php';

        $viewData = [
            'user'        => $user,
            'total'       => $total,
            'pendentes'   => $pendentes,
            'concluidas'  => $concluidas,
            'submissions' => $submissions,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }
}
