<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlAdminNotificationRepository;
use App\Support\Session;
use App\Support\Csrf;

/**
 * Controller for in-app notifications API and Views
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
     * Show all notifications for current admin (View)
     */
    public function index(array $vars = []): void
    {
        $admin = Session::get('admin');
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $notifications = $this->notificationRepo->findByUser((int) $admin['id'], $perPage, $offset);
        // We do not have a countAll method, so pagination will be simple (Next/Prev)

        $pageTitle = 'Todas as Notificações';
        $contentView = __DIR__ . '/../../View/admin/notifications/index.php';
        $viewData = [
            'notifications' => $notifications,
            'page' => $page,
            'hasMore' => count($notifications) === $perPage,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
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
