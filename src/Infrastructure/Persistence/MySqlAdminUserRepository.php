<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\AdminUserRepository;
use PDO;

final class MySqlAdminUserRepository implements AdminUserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admin_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare(
            "SELECT * FROM admin_users
             ORDER BY is_active DESC, full_name ASC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countAll(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO admin_users
                (full_name, email, role, is_active, password_hash,
                 azure_oid, azure_tenant_id, azure_upn, created_at)
                VALUES
                (:full_name, :email, :role, :is_active, :password_hash,
                 :azure_oid, :azure_tenant_id, :azure_upn, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':full_name'       => $data['full_name'],
            ':email'           => $data['email'],
            ':role'            => $data['role'] ?? 'ADMIN',
            ':is_active'       => $data['is_active'] ?? 1,
            ':password_hash'   => $data['password_hash'] ?? null,
            ':azure_oid'       => $data['azure_oid'] ?? null,
            ':azure_tenant_id' => $data['azure_tenant_id'] ?? null,
            ':azure_upn'       => $data['azure_upn'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        foreach ([
            'full_name',
            'email',
            'role',
            'is_active',
            'password_hash',
            'azure_oid',
            'azure_tenant_id',
            'azure_upn'
        ] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[]          = "{$col} = :{$col}";
                $params[":{$col}"] = $data[$col];
            }
        }

        if (!$fields) {
            return;
        }

        $sql = "UPDATE admin_users
                SET " . implode(', ', $fields) . ", updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
