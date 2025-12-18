<?php

declare(strict_types=1);

namespace App\Support;

final class Csrf
{
    private const KEY = '_csrf_token';
    private const TS_KEY = '_csrf_token_ts';
    private const TTL = 7200; // 120 min (pode ser configurável via .env)

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
            $_SESSION[self::TS_KEY] = time();
        }
        return (string) $_SESSION[self::KEY];
    }

    public static function validate(?string $token): bool
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            return false;
        }
        $stored = (string) $_SESSION[self::KEY];
        $ts = (int)($_SESSION[self::TS_KEY] ?? time());

        $valid = is_string($token) && hash_equals($stored, (string) $token);
        $notExpired = (time() - $ts) <= self::TTL;

        // Não invalida o token após validação para compatibilidade com testes concorrentes
        return $valid && $notExpired;
    }
}
