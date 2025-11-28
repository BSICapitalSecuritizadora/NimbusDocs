<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Support\Session;
use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Infrastructure\Persistence\MySqlAuditLogRepository;

final class DashboardAdminController
{
    private MySqlPortalSubmissionRepository $submissionRepo;
    private MySqlPortalUserRepository $portalUserRepo;
    private MySqlPortalAccessTokenRepository $tokenRepo;
    private MySqlAuditLogRepository $auditRepo;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];

        $this->submissionRepo = new MySqlPortalSubmissionRepository($pdo);
        $this->portalUserRepo = new MySqlPortalUserRepository($pdo);
        $this->tokenRepo      = new MySqlPortalAccessTokenRepository($pdo);
        $this->auditRepo      = new MySqlAuditLogRepository($pdo);
    }

    private function requireAdmin(): array
    {
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }
        return $admin;
    }

    public function index(): void
    {
        $admin = $this->requireAdmin();

        // KPIs
        $totalSubmissions      = $this->submissionRepo->countAll();
        $pendingSubmissions    = $this->submissionRepo->countByStatus('PENDENTE');
        $finishedSubmissions   = $this->submissionRepo->countByStatus('FINALIZADA');
        $totalPortalUsers      = $this->portalUserRepo->countAll();
        $validTokens           = $this->tokenRepo->countValid();
        $expiredTokens         = $this->tokenRepo->countExpired();

        // Listas
        $recentSubmissions = $this->submissionRepo->latest(5);
        $recentLogs        = $this->auditRepo->latest(5);

        $pageTitle   = 'Dashboard';
        $contentView = __DIR__ . '/../../View/admin/dashboard/index.php';

        $viewData = [
            'admin'              => $admin,
            'totalSubmissions'   => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'finishedSubmissions' => $finishedSubmissions,
            'totalPortalUsers'   => $totalPortalUsers,
            'validTokens'        => $validTokens,
            'expiredTokens'      => $expiredTokens,
            'recentSubmissions'  => $recentSubmissions,
            'recentLogs'         => $recentLogs,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }
}
