<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlPortalDocumentRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Lista todos os documentos com informações básicas do usuário
     * @return array<int,array>
     */
    public function getAll(): array
    {
        $sql = "SELECT d.*, u.full_name AS user_full_name, u.email AS user_email
                FROM portal_documents d
                LEFT JOIN portal_users u ON u.id = d.portal_user_id
                ORDER BY d.created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO portal_documents (portal_user_id, title, description, file_path, file_original_name, file_size, file_mime, created_by_admin) VALUES (:uid, :title, :description, :path, :original_name, :size, :mime, :admin)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uid'           => $data['portal_user_id'],
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':path'          => $data['file_path'],
            ':original_name' => $data['file_original_name'],
            ':size'          => $data['file_size'],
            ':mime'          => $data['file_mime'],
            ':admin'         => $data['created_by_admin'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM portal_documents WHERE portal_user_id = :uid ORDER BY created_at DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM portal_documents WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM portal_documents WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
