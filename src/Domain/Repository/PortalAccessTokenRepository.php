<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalAccessTokenRepository
{
    public function createToken(int $portalUserId, string $code, \DateTimeInterface $expiresAt): int;

    /** @return array<int, array> */
    public function listRecentByUser(int $portalUserId, int $limit = 10): array;

    public function revokePendingTokens(int $portalUserId): void;

    /**
     * Busca um token válido (PENDING, não expirado) pelo código, já com dados do usuário.
     *
     * Retorna array com chaves:
     *  - token_* (id, code, expires_at, status...)
     *  - user_*  (id, full_name, email, etc.)
     */
    public function findValidWithUserByCode(string $code): ?array;

    public function markAsUsed(int $tokenId, string $ip, string $userAgent): void;
}
