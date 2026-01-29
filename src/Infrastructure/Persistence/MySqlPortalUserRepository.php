<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

use App\Support\Encrypter; // Import

final class MySqlPortalUserRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Retorna todos os usuários (apenas campos essenciais) para selects
     * @return array<int,array{id:int,full_name:?string,email:?string}>
     */
    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT id, full_name, email FROM portal_users ORDER BY full_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM portal_users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $row = $this->decryptRow($row);
        }
        
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM portal_users")->fetchColumn();
    }

    public function countInactiveSince(int $days = 30): int
    {
        $sql = "SELECT COUNT(*)
                FROM portal_users
                WHERE (
                    last_login_at IS NULL OR last_login_at < DATE_SUB(NOW(), INTERVAL :days DAY)
                ) AND status <> 'INACTIVE'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
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
                ORDER BY status ASC, full_name ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        
        foreach ($rows as &$r) {
            $r = $this->decryptRow($r);
        }
        
        return $rows;
    }

    public function create(array $data): int
    {
        // Criptografa antes de salvar
        $doc = !empty($data['document_number']) ? Encrypter::encrypt($data['document_number']) : null;
        $phone = !empty($data['phone_number']) ? Encrypter::encrypt($data['phone_number']) : null;

        $sql = "INSERT INTO portal_users
                (full_name, email, document_number, phone_number, external_id, notes, status)
                VALUES
                (:full_name, :email, :document_number, :phone_number, :external_id, :notes, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':full_name'       => $data['full_name'],
            ':email'           => $data['email'] ?? null,
            ':document_number' => $doc,
            ':phone_number'    => $phone,
            ':external_id'     => $data['external_id'] ?? null,
            ':notes'           => $data['notes'] ?? null,
            ':status'          => $data['status'] ?? 'INVITED',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];

        // Se vier document_number, criptografa
        if (array_key_exists('document_number', $data)) {
            $val = $data['document_number'];
            if (!empty($val)) {
                $val = Encrypter::encrypt($val);
            }
            $fields[] = "document_number = :document_number";
            $params[':document_number'] = $val;
            unset($data['document_number']); // remove para não reprocessar no loop abaixo
        }

        // Se vier phone_number, criptografa
        if (array_key_exists('phone_number', $data)) {
             $val = $data['phone_number'];
             if (!empty($val)) {
                 $val = Encrypter::encrypt($val);
             }
             $fields[] = "phone_number = :phone_number";
             $params[':phone_number'] = $val;
             unset($data['phone_number']);
        }

        foreach (['full_name', 'email', 'external_id', 'notes', 'status'] as $col) {
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

    public function deactivate(int $id): void
    {
        $sql = "UPDATE portal_users SET status = 'INACTIVE', updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function recordLastLogin(int $id, string $method): void
    {
        $sql = "UPDATE portal_users 
                SET last_login_at = NOW(), 
                    last_login_method = :method,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':method' => $method,
        ]);
    }

    /**
     * Retorna todos os usuários ativos para notificações.
     * @return array<int,array{id:int,full_name:string,email:string}>
     */
    public function getActiveUsers(): array
    {
        $sql = "SELECT id, full_name, email 
                FROM portal_users 
                WHERE status = 'ACTIVE' AND email IS NOT NULL
                ORDER BY full_name ASC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function decryptRow(array $row): array
    {
        if (!empty($row['document_number'])) {
            $row['document_number'] = Encrypter::decrypt($row['document_number']);
        }
        if (!empty($row['phone_number'])) {
            $row['phone_number'] = Encrypter::decrypt($row['phone_number']);
        }
        return $row;
    }
}
