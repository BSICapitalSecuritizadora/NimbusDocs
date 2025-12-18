<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

/**
 * Repository for admin in-app notifications
 */
class MySqlAdminNotificationRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    /**
     * Create a new notification
     */
    public function create(int $adminUserId, string $type, string $title, ?string $message = null, ?string $link = null): int
    {
        $sql = "INSERT INTO admin_notifications (admin_user_id, type, title, message, link) VALUES (:admin_user_id, :type, :title, :message, :link)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'admin_user_id' => $adminUserId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Create notifications for all active admins
     */
    public function createForAllAdmins(string $type, string $title, ?string $message = null, ?string $link = null): int
    {
        $sql = "INSERT INTO admin_notifications (admin_user_id, type, title, message, link)
                SELECT id, :type, :title, :message, :link 
                FROM admin_users 
                WHERE status = 'ACTIVE'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);

        return $stmt->rowCount();
    }

    /**
     * Find unread notifications for a user
     */
    public function findUnreadByUser(int $adminUserId, int $limit = 20): array
    {
        $sql = "SELECT * FROM admin_notifications 
                WHERE admin_user_id = :admin_user_id AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT :lim";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('admin_user_id', $adminUserId, PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Count unread notifications for a user
     */
    public function countUnreadByUser(int $adminUserId): int
    {
        $sql = "SELECT COUNT(*) FROM admin_notifications WHERE admin_user_id = :admin_user_id AND is_read = 0";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['admin_user_id' => $adminUserId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Find all notifications for a user (with pagination)
     */
    public function findByUser(int $adminUserId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM admin_notifications 
                WHERE admin_user_id = :admin_user_id 
                ORDER BY created_at DESC 
                LIMIT :lim OFFSET :off";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('admin_user_id', $adminUserId, PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue('off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(int $id, int $adminUserId): bool
    {
        $sql = "UPDATE admin_notifications SET is_read = 1 WHERE id = :id AND admin_user_id = :admin_user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'admin_user_id' => $adminUserId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $adminUserId): int
    {
        $sql = "UPDATE admin_notifications SET is_read = 1 WHERE admin_user_id = :admin_user_id AND is_read = 0";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['admin_user_id' => $adminUserId]);

        return $stmt->rowCount();
    }

    /**
     * Delete old read notifications (cleanup)
     */
    public function deleteOldRead(int $daysOld = 30): int
    {
        $sql = "DELETE FROM admin_notifications WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('days', $daysOld, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
