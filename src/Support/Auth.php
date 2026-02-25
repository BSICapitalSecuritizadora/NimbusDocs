<?php

declare(strict_types=1);

namespace App\Support;

final class Auth
{
    public static function admin(): ?array
    {
        /** @var array|null $admin */
        $admin = Session::get('admin');

        return $admin ?: null;
    }

    public static function isAdmin(): bool
    {
        return Session::has('admin');
    }

    public static function getAdmin(): ?array
    {
        return self::admin();
    }

    public static function loginAdmin(array $admin): void
    {
        Session::set('admin', $admin);
    }

    public static function logoutAdmin(): void
    {
        Session::remove('admin');
    }

    public static function requireAdmin(): array
    {
        $admin = self::admin();
        if (!$admin) {
            header('Location: /admin/login');
            exit;
        }

        // bloqueia admin inativo
        if (!empty($admin['is_active']) && (int) $admin['is_active'] === 0) {
            Session::destroy();
            http_response_code(403);
            echo 'Conta administrativa desativada.';
            exit;
        }

        return $admin;
    }

    public static function requireRole(string ...$roles): array
    {
        $admin = self::requireAdmin();
        $role = $admin['role'] ?? 'ADMIN';

        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo 'Acesso negado.';
            exit;
        }

        return $admin;
    }

    /**
     * Verifica se o admin logado possui um dos perfis informados.
     */
    public static function hasRole(string ...$roles): bool
    {
        $admin = self::admin();
        if (!$admin) {
            return false;
        }
        $role = $admin['role'] ?? 'OPERATOR';

        return in_array($role, $roles, true);
    }

    /**
     * Retorna o role do admin logado.
     */
    public static function getRole(): string
    {
        $admin = self::admin();

        return $admin['role'] ?? 'OPERATOR';
    }

    /**
     * Verifica se o admin tem permissão para uma ação em um recurso.
     *
     * @param string $resource Ex: 'submissions', 'portal_users'
     * @param string $action Ex: 'view', 'create', 'edit', 'delete', 'export'
     */
    public static function can(string $resource, string $action): bool
    {
        $admin = self::admin();
        if (!$admin) {
            return false;
        }

        $role = $admin['role'] ?? 'OPERATOR';

        return PermissionService::can($role, $resource, $action);
    }

    /**
     * Exige permissão. Se não tiver, retorna 403.
     */
    public static function requirePermission(string $resource, string $action): array
    {
        $admin = self::requireAdmin();

        if (!self::can($resource, $action)) {
            http_response_code(403);
            echo 'Você não tem permissão para esta ação.';
            exit;
        }

        return $admin;
    }

    public static function portalUser(): ?array
    {
        /** @var array|null $user */
        $user = Session::get('portal_user');

        return $user ?: null;
    }

    public static function isPortalUser(): bool
    {
        return Session::has('portal_user');
    }

    public static function getPortalUser(): ?array
    {
        return self::portalUser();
    }

    public static function loginPortalUser(array $user): void
    {
        Session::set('portal_user', $user);
    }

    public static function logoutPortalUser(): void
    {
        Session::remove('portal_user');
    }

    public static function requirePortalUser(): array
    {
        $user = self::portalUser();
        if (!$user) {
            header('Location: /portal/login');
            exit;
        }

        if (!empty($user['is_active']) && (int) $user['is_active'] === 0) {
            Session::destroy();
            http_response_code(403);
            echo 'Seu acesso ao portal foi desativado, entre em contato com o administrador.';
            exit;
        }

        self::checkInactivity();

        return $user;
    }

    /**
     * Verifica inatividade da sessão (ex: 30 minutos).
     * Se expirado, força logout e redireciona.
     */
    private static function checkInactivity(int $timeoutMinutes = 30): void
    {
        $lastActivity = Session::get('last_activity');

        if ($lastActivity && (time() - $lastActivity > ($timeoutMinutes * 60))) {
            Session::destroy();
            Session::flash('error', 'Sessão expirada por inatividade.');
            header('Location: /portal/login');
            exit;
        }

        // Atualiza timestamp de atividade
        Session::put('last_activity', time());
    }

    /**
     * Exige que o login tenha ocorrido nos últimos $minutes.
     * Caso contrário, exige revalidação (por enquanto, redireciona para login).
     */
    public static function requireRecentLogin(int $minutes = 10): void
    {
        $loginTime = Session::get('login_time');

        // Se nunca logou (ou não tem timestamp), ou se passou do tempo
        if (!$loginTime || (time() - $loginTime > ($minutes * 60))) {
            // Em uma implementação completa, redirecionaria para tela de "Confirm Password"
            // Por simplicidade (MVP), forçamos logout/login novo para garantir segurança.
            Session::flash('error', 'Por segurança, faça login novamente para realizar esta ação.');
            Session::forget('portal_user'); // Força logout limpo
            header('Location: /portal/login');
            exit;
        }
    }
}
