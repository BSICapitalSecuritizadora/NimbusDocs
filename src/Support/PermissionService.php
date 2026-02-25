<?php

declare(strict_types=1);

namespace App\Support;

use PDO;

/**
 * Serviço de Permissões (RBAC)
 *
 * Verifica permissões baseadas em role → permission.
 * Cache em memória durante a requisição para evitar queries repetidas.
 */
final class PermissionService
{
    private static ?PDO $pdo = null;

    private static array $cache = [];

    private static array $permissionsCache = [];

    /**
     * Define a conexão PDO (chamado no bootstrap)
     */
    public static function setPdo(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * Verifica se um role tem permissão para executar uma ação em um recurso.
     *
     * @param string $role Role do usuário (SUPER_ADMIN, ADMIN, OPERATOR, AUDITOR)
     * @param string $resource Recurso (ex: 'submissions', 'users')
     * @param string $action Ação (ex: 'view', 'create', 'edit', 'delete')
     * @return bool
     */
    public static function can(string $role, string $resource, string $action): bool
    {
        // SUPER_ADMIN tem tudo
        if ($role === 'SUPER_ADMIN') {
            return true;
        }

        $cacheKey = "{$role}:{$resource}:{$action}";

        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        if (!self::$pdo) {
            // Fallback: Admin e Super_Admin têm tudo, outros só view
            $result = in_array($role, ['ADMIN', 'SUPER_ADMIN']) || $action === 'view';
            self::$cache[$cacheKey] = $result;

            return $result;
        }

        $sql = '
            SELECT COUNT(*) 
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.role = :role 
              AND p.resource = :resource 
              AND p.action = :action
        ';

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':role' => $role,
            ':resource' => $resource,
            ':action' => $action,
        ]);

        $result = (int) $stmt->fetchColumn() > 0;
        self::$cache[$cacheKey] = $result;

        return $result;
    }

    /**
     * Retorna todas as permissões de um role.
     *
     * @return array<array{resource: string, action: string}>
     */
    public static function getPermissions(string $role): array
    {
        if (isset(self::$permissionsCache[$role])) {
            return self::$permissionsCache[$role];
        }

        if (!self::$pdo) {
            return [];
        }

        $sql = '
            SELECT p.resource, p.action, p.description
            FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            WHERE rp.role = :role
            ORDER BY p.resource, p.action
        ';

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':role' => $role]);

        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        self::$permissionsCache[$role] = $permissions;

        return $permissions;
    }

    /**
     * Retorna todos os recursos e ações disponíveis.
     */
    public static function getAllPermissions(): array
    {
        if (!self::$pdo) {
            return [];
        }

        $sql = 'SELECT id, resource, action, description FROM permissions ORDER BY resource, action';

        return self::$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Atualiza permissões de um role.
     *
     * @param string $role
     * @param int[] $permissionIds
     */
    public static function setRolePermissions(string $role, array $permissionIds): void
    {
        if (!self::$pdo || $role === 'SUPER_ADMIN') {
            return; // SUPER_ADMIN não pode ser modificado
        }

        self::$pdo->beginTransaction();

        try {
            // Remove permissões atuais
            $stmt = self::$pdo->prepare('DELETE FROM role_permissions WHERE role = :role');
            $stmt->execute([':role' => $role]);

            // Adiciona novas
            $insert = self::$pdo->prepare('INSERT INTO role_permissions (role, permission_id) VALUES (:role, :pid)');

            foreach ($permissionIds as $pid) {
                $insert->execute([':role' => $role, ':pid' => (int) $pid]);
            }

            self::$pdo->commit();

            // Limpa cache
            self::$cache = [];
            unset(self::$permissionsCache[$role]);

        } catch (\Throwable $e) {
            self::$pdo->rollBack();

            throw $e;
        }
    }

    /**
     * Limpa o cache de permissões.
     */
    public static function clearCache(): void
    {
        self::$cache = [];
        self::$permissionsCache = [];
    }
}
