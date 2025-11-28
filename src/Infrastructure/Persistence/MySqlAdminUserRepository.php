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
                (name, email, password_hash, auth_mode, role, status, ms_object_id, ms_tenant_id, ms_upn)
                VALUES (:name, :email, :password_hash, :auth_mode, :role, :status, :ms_object_id, :ms_tenant_id, :ms_upn)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'] ?? null,
            ':auth_mode'     => $data['auth_mode'] ?? 'LOCAL_ONLY',
            ':role'          => $data['role'] ?? 'ADMIN',
            ':status'        => $data['status'] ?? 'ACTIVE',
            ':ms_object_id'  => $data['ms_object_id'] ?? null,
            ':ms_tenant_id'  => $data['ms_tenant_id'] ?? null,
            ':ms_upn'        => $data['ms_upn'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fieldsMap = [
            'name',
            'email',
            'password_hash',
            'auth_mode',
            'role',
            'status',
            'ms_object_id',
            'ms_tenant_id',
            'ms_upn'
        ];

        $parts  = [];
        $params = [':id' => $id];

        foreach ($fieldsMap as $col) {
            if (array_key_exists($col, $data)) {
                $parts[]          = "$col = :$col";
                $params[":$col"] = $data[$col];
            }
        }

        if (!$parts) {
            return;
        }

        $sql = "UPDATE admin_users SET " . implode(', ', $parts) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
