<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalAnnouncementRepository;
use App\Support\Auth;

final class PortalAnnouncementController
{
    private MySqlPortalAnnouncementRepository $announcementRepo;

    public function __construct(private array $config)
    {
        $this->announcementRepo = new MySqlPortalAnnouncementRepository($config['pdo']);
    }

    public function index(array $vars = []): void
    {
        $user = Auth::requirePortalUser();

        // Busca comunicados ativos para exibição no portal
        $announcements = $this->announcementRepo->activeForPortal();

        $pageTitle   = 'Avisos informados';
        $contentView = __DIR__ . '/../../View/portal/announcements/index.php';

        $viewData = [
            'user'          => $user,
            'total'         => count($announcements),
            'pendentes'     => count(array_filter($announcements, fn($a) => $a['level'] === 'warning')),
            'concluidas'    => count(array_filter($announcements, fn($a) => $a['level'] === 'success')),
            'submissions'   => $announcements, // @TODO: mudar nome de 'submissions' para 'announcements'
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }
}
