<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Support\Auth;
use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Infrastructure\Persistence\MySqlAuditLogRepository;
use App\Infrastructure\Persistence\MySqlPortalDocumentRepository;

final class DashboardAdminController
{
    private MySqlPortalSubmissionRepository $submissionRepo;
    private MySqlPortalUserRepository $portalUserRepo;
    private MySqlPortalAccessTokenRepository $tokenRepo;
    private MySqlAuditLogRepository $auditRepo;
    private MySqlPortalDocumentRepository $documentRepo;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];

        $this->submissionRepo = new MySqlPortalSubmissionRepository($pdo);
        $this->portalUserRepo = new MySqlPortalUserRepository($pdo);
        $this->tokenRepo      = new MySqlPortalAccessTokenRepository($pdo);
        $this->auditRepo      = new MySqlAuditLogRepository($pdo);
        $this->documentRepo   = new MySqlPortalDocumentRepository($pdo);
    }

    private function requireAdmin(): array
    {
        return Auth::requireAdmin();
    }

    public function index(): void
    {
        $admin = $this->requireAdmin();

        // KPIs
        $totalSubmissions     = $this->submissionRepo->countAll();
        $pendingSubmissions   = $this->submissionRepo->countByStatus('PENDING');
        $approvedSubmissions  = $this->submissionRepo->countByStatus('APPROVED');
        $rejectedSubmissions  = $this->submissionRepo->countByStatus('REJECTED');
        $totalPortalUsers     = $this->portalUserRepo->countAll();
        $publishedDocuments   = $this->documentRepo->countAll();

        // Listas
        $recentSubmissions = $this->submissionRepo->latest(5);
        $recentLogs        = $this->auditRepo->latest(10);

        // Gráficos
        $statusCounts = $this->submissionRepo->countsByStatuses(['APPROVED','REJECTED','PENDING','IN_REVIEW']);
        $dailyCounts  = $this->submissionRepo->countsPerDay(30);
        $docsPerMonth = $this->documentRepo->countsPerMonth(12);

        // Alertas
        $alerts = [
            'oldPending'      => $this->submissionRepo->countOlderPending(7),
            'expiredTokens'   => $this->tokenRepo->countExpired(),
            'veryLargeDocs'   => $this->documentRepo->countVeryLarge(50),
            'inactiveUsers30' => $this->portalUserRepo->countInactiveSince(30),
        ];

        $pageTitle   = 'Dashboard';
        $contentView = __DIR__ . '/../../View/admin/dashboard/index.php';

        $viewData = [
            'admin'              => $admin,
            'totalSubmissions'   => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'approvedSubmissions' => $approvedSubmissions,
            'rejectedSubmissions' => $rejectedSubmissions,
            'totalPortalUsers'   => $totalPortalUsers,
            'publishedDocuments' => $publishedDocuments,
            'recentSubmissions'  => $recentSubmissions,
            'recentLogs'         => $recentLogs,
            'chartStatusCounts'  => $statusCounts,
            'chartDailyCounts'   => $dailyCounts,
            'chartDocsPerMonth'  => $docsPerMonth,
            'alerts'             => $alerts,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }
}
