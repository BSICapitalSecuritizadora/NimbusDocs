<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlSettingsRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array<string,string>
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT `key`, `value` FROM app_settings');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = (string) $row['value'];
        }

        return $settings;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = $this->pdo->prepare('SELECT `value` FROM app_settings WHERE `key` = :k LIMIT 1');
        $stmt->execute([':k' => $key]);
        $value = $stmt->fetchColumn();

        return $value === false ? $default : (string) $value;
    }

    /**
     * @param array<string,string> $data
     */
    public function setMany(array $data): void
    {
        $sql = 'INSERT INTO app_settings (`key`, `value`, `updated_at`)
                VALUES (:k, :v, NOW())
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()';

        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->execute([
                ':k' => $key,
                ':v' => $value,
            ]);
        }
    }
}
