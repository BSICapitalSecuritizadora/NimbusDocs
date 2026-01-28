<?php

declare(strict_types=1);

namespace App\Support;

class RateLimiter
{
    private static ?FileCache $cache = null;

    private static function getCache(): FileCache
    {
        if (self::$cache === null) {
            // Define path to var/cache/rate_limiter relative to this file
            $cacheDir = dirname(__DIR__, 2) . '/var/cache/rate_limiter';
            self::$cache = new FileCache($cacheDir);
        }
        return self::$cache;
    }

    /**
     * Check if the key is allowed (has not exceeded max attempts).
     * This method does NOT increment the counter.
     */
    public static function isAllowed(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $tries = (int)self::getCache()->get('limit:' . $key, 0);
        return $tries < $maxAttempts;
    }

    /**
     * Increment the counter for the key.
     */
    public static function recordAttempt(string $key, int $decaySeconds): void
    {
        self::getCache()->increment('limit:' . $key, 1, $decaySeconds);
    }

    /**
     * Clear the counter for the key.
     */
    public static function reset(string $key): void
    {
        self::getCache()->delete('limit:' . $key);
    }

    /**
     * Get the estimated time remaining in seconds.
     * Since FileCache doesn't verify exact TTL easily, we return a default.
     */
    public static function getTimeRemaining(string $key): int
    {
        // Default to 15 minutes as used in controller
        return 900; 
    }
}
