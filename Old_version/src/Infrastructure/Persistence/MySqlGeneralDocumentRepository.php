<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlGeneralDocumentRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO general_documents
            (category_id, title, description, file_path, file_mime, file_size,
             file_original_name, is_active, published_at, created_by_admin)
            VALUES
            (:category_id, :title, :description, :file_path, :file_mime, :file_size,
             :file_original_name, :is_active, :published_at, :created_by_admin)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':category_id'        => $data['category_id'],
            ':title'              => $data['title'],
            ':description'        => $data['description'],
            ':file_path'          => $data['file_path'],
            ':file_mime'          => $data['file_mime'],
            ':file_size'          => $data['file_size'],
            ':file_original_name' => $data['file_original_name'],
            ':is_active'          => $data['is_active'],
            ':published_at'       => $data['published_at'],
            ':created_by_admin'   => $data['created_by_admin'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "
            UPDATE general_documents
               SET category_id        = :category_id,
                   title              = :title,
                   description        = :description,
                   is_active          = :is_active
             WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'          => $id,
            ':category_id' => $data['category_id'],
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':is_active'   => $data['is_active'],
        ]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT d.*, c.name AS category_name
            FROM general_documents d
            JOIN document_categories c ON c.id = d.category_id
            WHERE d.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function listForAdmin(): array
    {
        $stmt = $this->pdo->query("
            SELECT d.*, c.name AS category_name
            FROM general_documents d
            JOIN document_categories c ON c.id = d.category_id
            ORDER BY d.published_at DESC, d.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listForPortal(?int $categoryId = null, ?string $term = null): array
    {
        $where  = ['d.is_active = 1'];
        $params = [];

        if ($categoryId) {
            $where[]           = 'd.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        if ($term !== null && $term !== '') {
            $where[]           = '(d.title LIKE :term OR d.description LIKE :term)';
            $params[':term']   = '%' . $term . '%';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $sql = "
            SELECT d.*, c.name AS category_name
            FROM general_documents d
            JOIN document_categories c ON c.id = d.category_id
            {$whereSql}
            ORDER BY d.published_at DESC, d.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM general_documents WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
