<?php

declare(strict_types=1);

namespace App\Support;

final class Session
{
    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, $default = null)
    {
        $val = $_SESSION['_flash'][$key] ?? $default;
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        return $val;
    }

    public static function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }
}
