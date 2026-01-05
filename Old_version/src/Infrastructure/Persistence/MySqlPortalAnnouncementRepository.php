<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlPortalAnnouncementRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Lista avisos ativos para o portal (baseado em agora).
     * @return array<int,array<string,mixed>>
     */
    public function activeForPortal(): array
    {
        $now = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        $sql = "
            SELECT id, title, body, level, starts_at, ends_at
            FROM portal_announcements
            WHERE is_active = 1
              AND (starts_at IS NULL OR starts_at <= :now)
              AND (ends_at   IS NULL OR ends_at   >= :now)
            ORDER BY created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':now' => $now]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lista todos para o admin (pode depois virar paginação).
     * @return array<int,array<string,mixed>>
     */
    public function listAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM portal_announcements
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM portal_announcements
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO portal_announcements
            (title, body, level, starts_at, ends_at, is_active, created_by_admin)
            VALUES
            (:title, :body, :level, :starts_at, :ends_at, :is_active, :created_by_admin)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title'           => $data['title'],
            ':body'            => $data['body'],
            ':level'           => $data['level'],
            ':starts_at'       => $data['starts_at'],
            ':ends_at'         => $data['ends_at'],
            ':is_active'       => $data['is_active'],
            ':created_by_admin' => $data['created_by_admin'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "
            UPDATE portal_announcements
            SET title = :title,
                body = :body,
                level = :level,
                starts_at = :starts_at,
                ends_at   = :ends_at,
                is_active = :is_active
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'       => $id,
            ':title'    => $data['title'],
            ':body'     => $data['body'],
            ':level'    => $data['level'],
            ':starts_at' => $data['starts_at'],
            ':ends_at'  => $data['ends_at'],
            ':is_active' => $data['is_active'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM portal_announcements WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
    }
}
