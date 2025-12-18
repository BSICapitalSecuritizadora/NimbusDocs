<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Logging\RequestLogger;
use App\Support\Session;

/**
 * Dashboard de Monitoramento Avançado
 * Exibe estatísticas de requisições, alertas e performance
 */
final class MonitoringAdminController
{
    public function __construct(private array $config)
    {
    }

    /**
     * Exibe dashboard principal
     */
    public function index(array $vars = []): string
    {
        // Verifica autorização
        if (!Session::has('admin_user')) {
            header('Location: /admin/login');
            exit;
        }

        // Obtém dados para dashboard
        $stats = RequestLogger::getStatistics(24); // Últimas 24 horas
        $recentRequests = RequestLogger::getRecentRequests(50);
        $alerts = RequestLogger::getAlerts(30);

        // Filtra por tipo de alerta (opcional)
        $filter = $_GET['filter'] ?? 'all';
        if ($filter === 'errors') {
            $alerts = array_filter($alerts, fn($a) => $a['type'] === 'error');
        } elseif ($filter === 'unauthorized') {
            $alerts = array_filter($alerts, fn($a) => $a['type'] === 'unauthorized');
        } elseif ($filter === 'slow') {
            $alerts = array_filter($alerts, fn($a) => ($a['duration_ms'] ?? 0) > 5000);
        }

        // Calcula taxa de sucesso
        $successRate = $stats['total_requests'] > 0
            ? round(($stats['success'] / $stats['total_requests']) * 100, 2)
            : 0;

        // Calcula taxa de erro
        $errorRate = $stats['total_requests'] > 0
            ? round((($stats['errors'] + $stats['unauthorized']) / $stats['total_requests']) * 100, 2)
            : 0;

        // Views
        return $this->renderMonitoringDashboard([
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'alerts' => array_slice($alerts, 0, 30), // Últimos 30 alertas
            'successRate' => $successRate,
            'errorRate' => $errorRate,
            'filter' => $filter,
        ]);
    }

    /**
     * API endpoint para dados em tempo real (JSON)
     */
    public function apiStats(array $vars = []): string
    {
        if (!Session::has('admin_user')) {
            http_response_code(401);
            return json_encode(['error' => 'Unauthorized']);
        }

        $hours = intval($_GET['hours'] ?? '24');
        $stats = RequestLogger::getStatistics($hours);

        header('Content-Type: application/json');
        return json_encode($stats);
    }

    /**
     * API endpoint para alertas (JSON)
     */
    public function apiAlerts(array $vars = []): string
    {
        if (!Session::has('admin_user')) {
            http_response_code(401);
            return json_encode(['error' => 'Unauthorized']);
        }

        $alerts = RequestLogger::getAlerts(100);

        header('Content-Type: application/json');
        return json_encode($alerts);
    }

    /**
     * API endpoint para requisições recentes (JSON)
     */
    public function apiRequests(array $vars = []): string
    {
        if (!Session::has('admin_user')) {
            http_response_code(401);
            return json_encode(['error' => 'Unauthorized']);
        }

        $limit = intval($_GET['limit'] ?? '100');
        $requests = RequestLogger::getRecentRequests($limit);

        header('Content-Type: application/json');
        return json_encode($requests);
    }

    /**
     * Renderiza dashboard
     */
    private function renderMonitoringDashboard(array $data): string
    {
        ob_start();
        include __DIR__ . '/../../Presentation/View/admin/monitoring/index.php';
        return ob_get_clean();
    }
}
