<?php

declare(strict_types=1);

namespace App\Domain\Repository;

/**
 * Interface for password reset token repository
 */
interface PasswordResetRepository
{
    /**
     * Create a new password reset token
     */
    public function create(int $adminUserId, string $token, \DateTimeInterface $expiresAt): int;

    /**
     * Find a valid (not expired, not used) token
     */
    public function findValidByToken(string $token): ?array;

    /**
     * Mark token as used
     */
    public function markAsUsed(string $token): bool;

    /**
     * Delete all tokens for a user (used after successful reset)
     */
    public function deleteByUserId(int $adminUserId): int;

    /**
     * Delete expired tokens (cleanup)
     */
    public function deleteExpired(): int;
}
