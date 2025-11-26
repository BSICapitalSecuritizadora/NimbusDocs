<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\PortalUserRepository;
use PDO;

final class MySqlPortalUserRepository implements PortalUserRepository
{
    public function __construct(private PDO $pdo) {}

    public function paginate(int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $total = (int)$this->pdo
            ->query("SELECT COUNT(*) FROM portal_users")
            ->fetchColumn();

        $sql = "SELECT * FROM portal_users
                ORDER BY created_at DESC
                LIMIT :perPage OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'   => $items,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int)ceil($total / $perPage),
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM portal_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findActiveByLogin(string $identifier): ?array
    {
        $sql = "SELECT *
                FROM portal_users
                WHERE (email = :identifier OR document_number = :identifier)
                  AND status IN ('ACTIVE', 'INVITED')
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':identifier' => $identifier]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO portal_users
                (full_name, email, document_number, phone_number, external_id, notes, status, password_hash)
                VALUES (:full_name, :email, :document_number, :phone_number, :external_id, :notes, :status, :password_hash)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':full_name'       => $data['full_name'],
            ':email'           => $data['email'] ?? null,
            ':document_number' => $data['document_number'] ?? null,
            ':phone_number'    => $data['phone_number'] ?? null,
            ':external_id'     => $data['external_id'] ?? null,
            ':notes'           => $data['notes'] ?? null,
            ':status'          => $data['status'] ?? 'INVITED',
            ':password_hash'   => $data['password_hash'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE portal_users
                SET full_name = :full_name,
                    email = :email,
                    document_number = :document_number,
                    phone_number = :phone_number,
                    external_id = :external_id,
                    notes = :notes,
                    status = :status,
                    password_hash = COALESCE(:password_hash, password_hash),
                    password_changed_at = CASE WHEN :password_hash IS NULL THEN password_changed_at ELSE NOW() END
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'              => $id,
            ':full_name'       => $data['full_name'],
            ':email'           => $data['email'] ?? null,
            ':document_number' => $data['document_number'] ?? null,
            ':phone_number'    => $data['phone_number'] ?? null,
            ':external_id'     => $data['external_id'] ?? null,
            ':notes'           => $data['notes'] ?? null,
            ':status'          => $data['status'] ?? 'INVITED',
            ':password_hash'   => $data['password_hash'] ?? null,
        ]);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $sql = "UPDATE portal_users
                SET password_hash = :hash,
                    password_changed_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'   => $id,
            ':hash' => $passwordHash,
        ]);
    }

    public function updateLastLogin(int $id, string $method): void
    {
        $sql = "UPDATE portal_users
                SET last_login_at = NOW(),
                    last_login_method = :method
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'     => $id,
            ':method' => $method,
        ]);
    }

    public function updateLastLogin(int $id, string $method): void
    {
        $sql = "UPDATE portal_users
                SET last_login_at = NOW(),
                    last_login_method = :method
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id'     => $id,
            ':method' => $method,
        ]);
    }

    public function deactivate(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE portal_users SET status = 'INACTIVE' WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
