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
            "SELECT id, 
                    COALESCE(name, full_name) AS name,
                    email, role, status, last_login_at, last_login_provider,
                    created_at, updated_at
             FROM admin_users
             ORDER BY status = 'ACTIVE' DESC, name ASC
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
                (name, email, full_name, password_hash, auth_mode, role, is_active, status,
                 azure_oid, azure_tenant_id, azure_upn, ms_object_id, ms_tenant_id, ms_upn)
                VALUES (:name, :email, :full_name, :password_hash, :auth_mode, :role, :is_active, :status,
                 :azure_oid, :azure_tenant_id, :azure_upn, :ms_object_id, :ms_tenant_id, :ms_upn)";

        $name = $data['name'] ?? $data['full_name'] ?? '';
        $isActive = ($data['status'] ?? 'ACTIVE') === 'ACTIVE' ? 1 : 0;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name'            => $name,
            ':email'           => $data['email'],
            ':full_name'       => $name,
            ':password_hash'   => $data['password_hash'] ?? null,
            ':auth_mode'       => $data['auth_mode'] ?? 'LOCAL_ONLY',
            ':role'            => $data['role'] ?? 'ADMIN',
            ':is_active'       => $isActive,
            ':status'          => $data['status'] ?? 'ACTIVE',
            ':azure_oid'       => $data['azure_oid'] ?? $data['ms_object_id'] ?? null,
            ':azure_tenant_id' => $data['azure_tenant_id'] ?? $data['ms_tenant_id'] ?? null,
            ':azure_upn'       => $data['azure_upn'] ?? $data['ms_upn'] ?? null,
            ':ms_object_id'    => $data['ms_object_id'] ?? $data['azure_oid'] ?? null,
            ':ms_tenant_id'    => $data['ms_tenant_id'] ?? $data['azure_tenant_id'] ?? null,
            ':ms_upn'          => $data['ms_upn'] ?? $data['azure_upn'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $parts  = [];
        $params = [':id' => $id];

        // Sync name/full_name
        if (array_key_exists('name', $data)) {
            $parts[] = 'name = :name, full_name = :name';
            $params[':name'] = $data['name'];
        }

        if (array_key_exists('email', $data)) {
            $parts[] = 'email = :email';
            $params[':email'] = $data['email'];
        }

        if (array_key_exists('password_hash', $data)) {
            $parts[] = 'password_hash = :password_hash';
            $params[':password_hash'] = $data['password_hash'];
        }

        if (array_key_exists('auth_mode', $data)) {
            $parts[] = 'auth_mode = :auth_mode';
            $params[':auth_mode'] = $data['auth_mode'];
        }

        if (array_key_exists('role', $data)) {
            $parts[] = 'role = :role';
            $params[':role'] = $data['role'];
        }

        // Sync status/is_active
        if (array_key_exists('status', $data)) {
            $isActive = $data['status'] === 'ACTIVE' ? 1 : 0;
            $parts[] = 'status = :status, is_active = :is_active';
            $params[':status'] = $data['status'];
            $params[':is_active'] = $isActive;
        }

        // Sync azure_*/ms_* fields
        if (array_key_exists('ms_object_id', $data)) {
            $parts[] = 'ms_object_id = :ms_object_id, azure_oid = :ms_object_id';
            $params[':ms_object_id'] = $data['ms_object_id'];
        }

        if (array_key_exists('ms_tenant_id', $data)) {
            $parts[] = 'ms_tenant_id = :ms_tenant_id, azure_tenant_id = :ms_tenant_id';
            $params[':ms_tenant_id'] = $data['ms_tenant_id'];
        }

        if (array_key_exists('ms_upn', $data)) {
            $parts[] = 'ms_upn = :ms_upn, azure_upn = :ms_upn';
            $params[':ms_upn'] = $data['ms_upn'];
        }

        if (!$parts) {
            return;
        }

        $sql = "UPDATE admin_users SET " . implode(', ', $parts) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
