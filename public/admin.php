<?php

declare(strict_types=1);

use App\Presentation\Controller\Admin\Auth\LoginController;
use App\Presentation\Controller\Admin\AdminUserController;
use App\Presentation\Controller\Admin\PortalUserController;
use App\Presentation\Controller\Admin\SubmissionAdminController;
use App\Presentation\Controller\Admin\FileAdminController;
use App\Presentation\Controller\Admin\AuditLogController;
use App\Presentation\Controller\Admin\AdminMicrosoftAuthController;
use App\Presentation\Controller\Admin\AdminUserAdminController;
use App\Presentation\Controller\Admin\DashboardAdminController;
use App\Presentation\Controller\Admin\TokenAdminController;
use App\Presentation\Controller\Admin\SettingsController;
use App\Support\Session;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Bootstrap da aplicação (autoload, .env, sessão, config, PDO...)
$config = require __DIR__ . '/../bootstrap/app.php';

// Dispatcher de rotas (FastRoute)
$dispatcher = simpleDispatcher(function (RouteCollector $r): void {
    // Login
    $r->addRoute('GET',  '/admin/login', [LoginController::class, 'showLoginForm']);
    $r->addRoute('POST', '/admin/login', [LoginController::class, 'handleLogin']);
    $r->addRoute('GET',  '/admin/logout', [LoginController::class, 'logout']);

    // Administradores (sistema)
    $r->addRoute('GET',  '/admin/users',              [AdminUserAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/users/create',       [AdminUserAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/users',              [AdminUserAdminController::class, 'store']);
    $r->addRoute('GET',  '/admin/users/{id:\d+}/edit', [AdminUserAdminController::class, 'editForm']);
    $r->addRoute('POST', '/admin/users/{id:\d+}',     [AdminUserAdminController::class, 'update']);
    $r->addRoute('GET',  '/admin/submissions',          [SubmissionAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/submissions/{id:\d+}', [SubmissionAdminController::class, 'show']);
    $r->addRoute('POST', '/admin/submissions/{id:\d+}/status', [SubmissionAdminController::class, 'updateStatus']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}/access-link', [PortalUserController::class, 'generateAccessCode']);
    $r->addRoute('POST', '/admin/submissions/{id:\d+}/response-files', [SubmissionAdminController::class, 'uploadResponseFiles']);
    $r->addRoute('GET',  '/admin/admin-users',             [AdminUserAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/admin-users/create',      [AdminUserAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/admin-users',             [AdminUserAdminController::class, 'store']);
    $r->addRoute('GET',  '/admin/admin-users/{id:\d+}/edit', [AdminUserAdminController::class, 'editForm']);
    $r->addRoute('POST', '/admin/admin-users/{id:\d+}',    [AdminUserAdminController::class, 'update']);
    $r->addRoute('GET', '/admin/dashboard', [DashboardAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/tokens',             [TokenAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/tokens/{id:\d+}',    [TokenAdminController::class, 'show']);
    $r->addRoute('POST', '/admin/tokens/{id:\d+}/revoke', [TokenAdminController::class, 'revoke']);
    $r->addRoute('GET',  '/admin/settings',                      [SettingsController::class, 'index']);
    $r->addRoute('GET',  '/admin/settings/notifications',        [SettingsController::class, 'notificationsForm']);
    $r->addRoute('POST', '/admin/settings/notifications/save',   [SettingsController::class, 'saveNotifications']);
    $r->addRoute('GET', '/admin/submissions/export/csv', [SubmissionAdminController::class, 'exportCsv']);

    // Usuários Finais
    $r->addRoute('GET',  '/admin/portal-users',                    [PortalUserController::class, 'index']);
    $r->addRoute('GET',  '/admin/portal-users/create',             [PortalUserController::class, 'showCreateForm']);
    $r->addRoute('POST', '/admin/portal-users',                    [PortalUserController::class, 'store']);
    $r->addRoute('GET',  '/admin/portal-users/{id:\d+}/edit',      [PortalUserController::class, 'showEditForm']);
    $r->addRoute('GET',  '/admin/portal-users/{id:\d+}', [PortalUserController::class, 'show']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}',           [PortalUserController::class, 'update']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}/deactivate', [PortalUserController::class, 'deactivate']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}/tokens', [PortalUserController::class, 'generateAccessCode']);

    // Login com Microsoft (admin)
    $r->addRoute('GET', '/admin/login/microsoft', [AdminMicrosoftAuthController::class, 'redirectToProvider']);
    $r->addRoute('GET', '/admin/auth/callback',   [AdminMicrosoftAuthController::class, 'handleCallback']);

    // Downloads de arquivos de submissão
    $r->addRoute('GET', '/admin/files/{id:\d+}/download', [FileAdminController::class, 'download']);

    // Auditoria
    $r->addRoute('GET', '/admin/audit-logs', [AuditLogController::class, 'index']);

    // Dashboard
    $r->addRoute('GET', '/admin', function () {
        echo '<div style="font-family:system-ui;padding:2rem">
                <h2>Bem-vindo ao NimbusDocs Admin</h2>
                <p>Login efetuado com sucesso.</p>
              </div>';
    });
});


// Descobre método e URI
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri        = $_SERVER['REQUEST_URI']    ?? '/';

// Remove query string ANTES de qualquer validação de rota
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// --------------------
// Proteção de rotas admin
// --------------------
$publicRoutes = [
    ['GET',  '/admin/login'],
    ['POST', '/admin/login'],
    ['GET',  '/admin/login/microsoft'],
    ['GET',  '/admin/auth/callback'],
];

$isPublic = false;
foreach ($publicRoutes as [$method, $path]) {
    if ($httpMethod === $method && $uri === $path) {
        $isPublic = true;
        break;
    }
}

// Se não for rota pública e não tiver admin logado, redireciona
if (str_starts_with($uri, '/admin') && !$isPublic && !Session::has('admin')) {
    header('Location: /admin/login');
    exit;
}

// Faz o dispatch
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Página não encontrada';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Método não permitido';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars    = $routeInfo[2];

        // 1) Se o handler for um array [Controller::class, 'method']
        if (is_array($handler) && isset($handler[0], $handler[1])) {
            [$class, $method] = $handler;

            $controller = new $class($config);
            $response   = $controller->$method($vars);

            if (is_string($response)) {
                echo $response;
            }
            break;
        }

        // 2) Se for uma Closure / callable simples
        if (is_callable($handler)) {
            echo $handler(...array_values($vars));
            break;
        }

        // 3) Fallback (não deveria chegar aqui)
        http_response_code(500);
        echo '500 - Handler inválido';
        break;
}
