<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface PortalAccessTokenRepository
{
    /**
     * Cria um novo token de acesso para o usuário do portal.
     *
     * Espera um array com chaves, por exemplo:
     *  - portal_user_id (int)
     *  - token (string)
     *  - expires_at (string Y-m-d H:i:s)
     *  - created_by_admin_id (int|null)
     */
    public function create(array $data): int;

    /**
     * Busca um token válido (não usado e não expirado) pelo valor do token.
     *
     * Retorna um array com os campos da tabela portal_access_tokens
     * ou null se não houver token válido.
     */
    public function findValidToken(string $token): ?array;

    /**
     * Marca o token como utilizado (uso único).
     */
    public function markAsUsed(int $id): void;

    /**
     * Lista tokens recentes de um usuário do portal.
     *
     * @return array<int, array>
     */
    public function listRecentForUser(int $portalUserId, int $limit = 10): array;

    /**
     * Invalida/marca como usados todos os tokens pendentes de um usuário
     * (usado quando vamos gerar um novo link e queremos que os antigos
     * não funcionem mais).
     */
    public function invalidateOldTokensForUser(int $portalUserId): void;
}
