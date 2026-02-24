<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\ReportScheduleRepository;
use PDO;

final class MySqlReportScheduleRepository implements ReportScheduleRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM report_schedules ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findDueSchedules(): array
    {
        $sql = "SELECT * FROM report_schedules 
                WHERE is_active = 1 AND next_run_at <= NOW()
                ORDER BY next_run_at ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM report_schedules WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO report_schedules 
                (report_type, frequency, recipient_emails, next_run_at, is_active)
                VALUES (:report_type, :frequency, :recipient_emails, :next_run_at, :is_active)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':report_type'      => $data['report_type'],
            ':frequency'        => $data['frequency'],
            ':recipient_emails' => $data['recipient_emails'], // Should be JSON encoded by controller
            ':next_run_at'      => $data['next_run_at'],
            ':is_active'        => (int)($data['is_active'] ?? 1),
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE report_schedules 
                SET report_type = :report_type,
                    frequency = :frequency,
                    recipient_emails = :recipient_emails,
                    next_run_at = :next_run_at,
                    is_active = :is_active
                WHERE id = :id";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':report_type'      => $data['report_type'],
            ':frequency'        => $data['frequency'],
            ':recipient_emails' => $data['recipient_emails'],
            ':next_run_at'      => $data['next_run_at'],
            ':is_active'        => (int)$data['is_active'],
            ':id'               => $id,
        ]);
    }
    
    public function updateRunTimes(int $id, string $lastRunAt, string $nextRunAt): void
    {
        $sql = "UPDATE report_schedules 
                SET last_run_at = :last_run_at, next_run_at = :next_run_at 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':last_run_at' => $lastRunAt,
            ':next_run_at' => $nextRunAt,
            ':id'          => $id,
        ]);
    }

    public function toggleActive(int $id, bool $isActive): void
    {
        $stmt = $this->pdo->prepare("UPDATE report_schedules SET is_active = :active WHERE id = :id");
        $stmt->execute([
            ':active' => (int)$isActive,
            ':id'     => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM report_schedules WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
