<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlAdminNotificationRepository;
use App\Support\Session;

/**
 * Controller for in-app notifications API
 */
class InAppNotificationController
{
    private array $config;

    private MySqlAdminNotificationRepository $notificationRepo;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->notificationRepo = new MySqlAdminNotificationRepository($config['pdo']);
    }

    /**
     * Get unread notifications for current admin (AJAX)
     */
    public function getUnread(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);

            return;
        }

        $notifications = $this->notificationRepo->findUnreadByUser((int) $admin['id'], 10);
        $count = $this->notificationRepo->countUnreadByUser((int) $admin['id']);

        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'count' => $count,
        ]);
    }

    /**
     * Mark a notification as read (AJAX)
     */
    public function markAsRead(array $params): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);

            return;
        }

        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid notification ID']);

            return;
        }

        $success = $this->notificationRepo->markAsRead($id, (int) $admin['id']);

        echo json_encode([
            'success' => $success,
        ]);
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllAsRead(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);

            return;
        }

        $count = $this->notificationRepo->markAllAsRead((int) $admin['id']);

        echo json_encode([
            'success' => true,
            'marked' => $count,
        ]);
    }
}
