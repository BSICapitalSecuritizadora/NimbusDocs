<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;
use App\Support\Auth;

/**
 * Controller para métricas e monitoramento da fila de notificações.
 */
final class NotificationMetricsController
{
    private MySqlNotificationOutboxRepository $outbox;

    public function __construct(private array $config)
    {
        $this->outbox = new MySqlNotificationOutboxRepository($config['pdo'], $config['logger'] ?? null);
    }

    /**
     * Página de métricas do worker de notificações.
     */
    public function index(): void
    {
        Auth::requireAdmin();

        $metrics          = $this->outbox->getMetrics();
        $failuresByType   = $this->outbox->getFailuresByType();
        $volumeByDay      = $this->outbox->getVolumeByDay(7);
        $avgProcessingTime = $this->outbox->getAverageProcessingTime();
        $deadLetterQueue  = $this->outbox->getDeadLetterQueue(20);

        $pageTitle   = 'Métricas de Notificações';
        $contentView = __DIR__ . '/../../View/admin/notifications/metrics.php';
        $viewData = [
            'metrics'           => $metrics,
            'failuresByType'    => $failuresByType,
            'volumeByDay'       => $volumeByDay,
            'avgProcessingTime' => $avgProcessingTime,
            'deadLetterQueue'   => $deadLetterQueue,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    /**
     * API JSON para métricas (polling/dashboard).
     */
    public function apiMetrics(): void
    {
        Auth::requireAdmin();

        $metrics           = $this->outbox->getMetrics();
        $avgProcessingTime = $this->outbox->getAverageProcessingTime();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => [
                'backlog'            => $metrics['backlog'],
                'sending'            => $metrics['sending'],
                'sent_today'         => $metrics['sent_today'],
                'failed_today'       => $metrics['failed_today'],
                'failed_total'       => $metrics['failed_total'],
                'avg_processing_sec' => $avgProcessingTime,
                'timestamp'          => date('Y-m-d H:i:s'),
            ],
        ], JSON_THROW_ON_ERROR);
        exit;
    }
}
