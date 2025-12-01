<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlPortalUserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM portal_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM portal_users")->fetchColumn();
    }

    /**
     * @return array<int,array>
     */
    public function paginate(int $page, int $perPage, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];

        $where = '';
        if ($search !== null && $search !== '') {
            $where = "WHERE (full_name LIKE :s OR email LIKE :s)";
            $params[':s'] = '%' . $search . '%';
        }

        $sql = "SELECT *
                FROM portal_users
                {$where}
                ORDER BY is_active DESC, full_name ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO portal_users
                (full_name, email, document, is_active, created_at)
                VALUES
                (:full_name, :email, :document, :is_active, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email'     => $data['email'],
            ':document'  => $data['document'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['full_name', 'email', 'document', 'is_active'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[]          = "{$col} = :{$col}";
                $params[":{$col}"] = $data[$col];
            }
        }

        if (!$fields) {
            return;
        }

        $sql = "UPDATE portal_users
                SET " . implode(', ', $fields) . ", updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
