<?php

declare(strict_types=1);

namespace App\Support;

final class Url
{
    public static function base(): string
    {
        return rtrim(getenv('APP_URL') ?: 'https://nimbusdocs.local', '/');
    }

    public static function portal(string $path = ''): string
    {
        $base = self::base() . '/portal';
        $path = '/' . ltrim($path, '/');
        return $base . $path;
    }

    public static function admin(string $path = ''): string
    {
        $base = self::base() . '/admin';
        $path = '/' . ltrim($path, '/');
        return $base . $path;
    }
}
