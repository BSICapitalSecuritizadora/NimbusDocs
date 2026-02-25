<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Queue\QueueInterface;
use PDO;

class DatabaseQueue implements QueueInterface
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * Push a new job onto the queue.
     */
    public function push(string $jobClass, array $data = [], string $queue = 'default'): int
    {
        $payload = json_encode([
            'job' => $jobClass,
            'data' => $data,
        ], JSON_THROW_ON_ERROR);

        $sql = 'INSERT INTO jobs (queue, payload, created_at, available_at) VALUES (:queue, :payload, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':queue' => $queue,
            ':payload' => $payload,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Pop the next available job from the queue.
     *
     * Uses atomic locking:
     * 1. UPDATE ... LIMIT 1 to reserve
     * 2. SELECT ... to fetch
     *
     * @param string $queue
     * @return array|null The raw job row or null if empty.
     */
    public function pop(string $queue = 'default'): ?array
    {
        // Atomically pop a job using SELECT ... FOR UPDATE (Pessimistic Locking)

        $this->pdo->beginTransaction();

        try {
            // 1. Find and Lock candidate
            $sql = 'SELECT * FROM jobs 
                    WHERE queue = :queue 
                      AND reserved_at IS NULL 
                      AND available_at <= :now 
                    ORDER BY id ASC 
                    LIMIT 1 
                    FOR UPDATE';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':queue' => $queue, ':now' => date('Y-m-d H:i:s')]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$job) {
                // No jobs available
                $this->pdo->commit();

                return null;
            }

            // 2. Reserve it
            $id = (int) $job['id'];
            $update = $this->pdo->prepare('UPDATE jobs SET reserved_at = NOW(), attempts = attempts + 1 WHERE id = :id');
            $update->execute([':id' => $id]);

            $this->pdo->commit();

            // Return updated job data (locally updated fields)
            $job['reserved_at'] = date('Y-m-d H:i:s');
            $job['attempts'] = (int) $job['attempts'] + 1;

            return $job;

        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            throw $e;
        }
    }

    /**
     * Mark a job as completed (delete it).
     */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM jobs WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * Release a job back to the queue (e.g. after failure).
     */
    public function release(int $id, int $delaySeconds = 0): void
    {
        $availableAt = date('Y-m-d H:i:s', time() + $delaySeconds);
        $stmt = $this->pdo->prepare('UPDATE jobs SET reserved_at = NULL, available_at = :avail WHERE id = :id');
        $stmt->execute([':avail' => $availableAt, ':id' => $id]);
    }

    /**
     * Mark a job as failed permanently.
     * (In this simple implementation, we might just leave it in DB or move to a failed_jobs table.
     * For now, we'll just release it with a far future date or handle it in worker)
     *
     * Ideally, we should have a `failed_jobs` table. For this iteration, we will rely on `max_attempts` check in worker.
     */
}
