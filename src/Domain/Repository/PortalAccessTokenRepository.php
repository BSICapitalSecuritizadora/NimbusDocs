<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalAccessTokenRepository
{
    public function createToken(int $portalUserId, string $code, \DateTimeInterface $expiresAt): int;

    /** @return array<int, array> */
    public function listRecentByUser(int $portalUserId, int $limit = 10): array;

    public function revokePendingTokens(int $portalUserId): void;
}
