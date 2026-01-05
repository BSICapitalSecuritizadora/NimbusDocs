<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class Connection
{
    public static function make(array $db): PDO
    {
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $db['driver'],
            $db['host'],
            (int)$db['port'],
            $db['database'],
            $db['charset'] ?? 'utf8mb4'
        );

        return new PDO($dsn, $db['username'], $db['password'], $db['options'] ?? []);
    }
}
