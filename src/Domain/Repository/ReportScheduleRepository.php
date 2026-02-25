<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface ReportScheduleRepository
{
    /**
     * @return array<int, array>
     */
    public function findAll(): array;

    /**
     * @return array<int, array>
     */
    public function findDueSchedules(): array;

    public function findById(int $id): ?array;

    public function create(array $data): int;

    public function update(int $id, array $data): void;

    public function updateRunTimes(int $id, string $lastRunAt, string $nextRunAt): void;

    public function toggleActive(int $id, bool $isActive): void;

    public function delete(int $id): void;
}
