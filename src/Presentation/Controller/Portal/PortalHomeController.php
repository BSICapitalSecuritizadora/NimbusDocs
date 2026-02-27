<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Support\Auth;

final class PortalHomeController
{
    private MySqlPortalSubmissionRepository $submissionRepo;
    private \App\Infrastructure\Persistence\MySqlPortalAnnouncementRepository $announcementRepo;

    public function __construct(private array $config)
    {
        $this->submissionRepo = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->announcementRepo = new \App\Infrastructure\Persistence\MySqlPortalAnnouncementRepository($config['pdo']);
    }

    public function index(array $vars = []): void
    {
        $user = Auth::requirePortalUser();
        $userId = (int) $user['id'];

        // KPIs do usuário
        $total = $this->submissionRepo->countForUser($userId);
        $pendentes = $this->submissionRepo->countForUserByStatus($userId, 'PENDENTE');
        $concluidas = $this->submissionRepo->countForUserByStatus($userId, 'FINALIZADA');
        
        $stats = [
            'total' => $total,
            'pending' => $pendentes,
            'approved' => $concluidas,
        ];

        // Últimas submissões
        $submissions = $this->submissionRepo->latestForUser($userId, 10);
        
        // Comunicados
        $announcements = $this->announcementRepo->activeForPortal();

        $pageTitle = 'Minhas informações';
        $contentView = __DIR__ . '/../../View/portal/dashboard/index.php';

        $viewData = [
            'user' => $user,
            'stats' => $stats,
            'total' => $total,
            'pendentes' => $pendentes,
            'concluidas' => $concluidas,
            'submissions' => $submissions,
            'announcements' => $announcements,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }
}
