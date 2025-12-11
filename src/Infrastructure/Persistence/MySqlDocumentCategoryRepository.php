<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlDocumentCategoryRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, name, description, sort_order, created_at
            FROM document_categories
            ORDER BY sort_order ASC, name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, sort_order, created_at
            FROM document_categories
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO document_categories (name, description, sort_order)
            VALUES (:name, :description, :sort_order)
        ");
        $stmt->execute([
            ':name'        => $data['name'],
            ':description' => $data['description'],
            ':sort_order'  => $data['sort_order'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE document_categories
            SET name = :name,
                description = :description,
                sort_order = :sort_order
            WHERE id = :id
        ");
        $stmt->execute([
            ':id'          => $id,
            ':name'        => $data['name'],
            ':description' => $data['description'],
            ':sort_order'  => $data['sort_order'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM document_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
