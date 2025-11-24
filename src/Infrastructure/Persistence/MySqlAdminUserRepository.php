<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\AdminUserRepository;
use PDO;

final class MySqlAdminUserRepository implements AdminUserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findActiveByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM admin_users
                WHERE email = :email AND status = 'ACTIVE'
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateLastLogin(int $id, string $provider): void
    {
        $sql = "UPDATE admin_users
                SET last_login_at = NOW(), last_login_provider = :p
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':p' => $provider, ':id' => $id]);
    }

    // ---------- CRUD ----------

    public function paginate(int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $total = (int)$this->pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();

        $sql = "SELECT * FROM admin_users
                ORDER BY created_at DESC
                LIMIT :perPage OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admin_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
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
            ':auth_mode'     => $data['auth_mode'],
            ':role'          => $data['role'],
            ':status'        => $data['status'] ?? 'ACTIVE',
            ':ms_object_id'  => $data['ms_object_id'] ?? null,
            ':ms_tenant_id'  => $data['ms_tenant_id'] ?? null,
            ':ms_upn'        => $data['ms_upn'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [
            'name'      => ':name',
            'email'     => ':email',
            'auth_mode' => ':auth_mode',
            'role'      => ':role',
            'status'    => ':status',
        ];

        $setParts = [];
        $params   = [':id' => $id];

        foreach ($fields as $column => $placeholder) {
            if (array_key_exists($column, $data)) {
                $setParts[]          = "$column = $placeholder";
                $params[$placeholder] = $data[$column];
            }
        }

        if (array_key_exists('password_hash', $data)) {
            $setParts[]                 = "password_hash = :password_hash";
            $params[':password_hash']   = $data['password_hash'];
        }

        if (!$setParts) {
            return;
        }

        $sql = "UPDATE admin_users SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function deactivate(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE admin_users SET status = 'INACTIVE' WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
