<?php

declare(strict_types=1);

use App\Presentation\Controller\Admin\Auth\LoginController;
use App\Presentation\Controller\Admin\AdminUserController;
use App\Presentation\Controller\Admin\PortalUserController;
use App\Presentation\Controller\Admin\SubmissionAdminController;
use App\Presentation\Controller\Admin\FileAdminController;
use App\Presentation\Controller\Admin\AuditLogController;
use App\Presentation\Controller\Admin\AdminUserAdminController;
use App\Presentation\Controller\Admin\DashboardAdminController;
use App\Presentation\Controller\Admin\TokenAdminController;
use App\Presentation\Controller\Admin\SettingsController;
use App\Presentation\Controller\Admin\DocumentAdminController;
use App\Presentation\Controller\Admin\AdminAuthController;
use App\Presentation\Controller\Admin\AnnouncementAdminController;
use App\Presentation\Controller\Admin\PortalAccessLogAdminController;
use App\Presentation\Controller\Admin\ReportsAdminController;
use App\Presentation\Controller\Admin\DocumentCategoryAdminController;
use App\Presentation\Controller\Admin\GeneralDocumentAdminController;
use App\Presentation\Controller\Admin\NotificationOutboxAdminController;
use App\Presentation\Controller\Admin\MonitoringAdminController;
use App\Infrastructure\Logging\RequestLogger;
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

    // Password Recovery
    $r->addRoute('GET',  '/admin/forgot-password', [\App\Presentation\Controller\Admin\PasswordResetController::class, 'showForgotForm']);
    $r->addRoute('POST', '/admin/forgot-password', [\App\Presentation\Controller\Admin\PasswordResetController::class, 'sendResetLink']);
    $r->addRoute('GET',  '/admin/reset-password/{token}', [\App\Presentation\Controller\Admin\PasswordResetController::class, 'showResetForm']);
    $r->addRoute('POST', '/admin/reset-password', [\App\Presentation\Controller\Admin\PasswordResetController::class, 'resetPassword']);

    // Two-Factor Authentication
    $r->addRoute('GET',  '/admin/2fa/setup', [\App\Presentation\Controller\Admin\TwoFactorController::class, 'showSetup']);
    $r->addRoute('POST', '/admin/2fa/enable', [\App\Presentation\Controller\Admin\TwoFactorController::class, 'enable']);
    $r->addRoute('POST', '/admin/2fa/disable', [\App\Presentation\Controller\Admin\TwoFactorController::class, 'disable']);
    $r->addRoute('GET',  '/admin/2fa/verify', [\App\Presentation\Controller\Admin\TwoFactorController::class, 'showVerify']);
    $r->addRoute('POST', '/admin/2fa/verify', [\App\Presentation\Controller\Admin\TwoFactorController::class, 'verify']);


    // Administradores (sistema)
    $r->addRoute('GET',  '/admin/users',              [AdminUserAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/users/create',       [AdminUserAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/users',              [AdminUserAdminController::class, 'store']);
    $r->addRoute('GET',  '/admin/users/{id:\d+}/edit', [AdminUserAdminController::class, 'editForm']);
    $r->addRoute('POST', '/admin/users/{id:\d+}',     [AdminUserAdminController::class, 'update']);
    // Desativar administrador (POST real) e GET para redirecionar com aviso
    $r->addRoute('POST', '/admin/users/{id:\d+}/deactivate', [AdminUserAdminController::class, 'deactivate']);
    $r->addRoute('GET',  '/admin/users/{id:\d+}/deactivate',  [AdminUserAdminController::class, 'deactivate']);
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
    $r->addRoute('GET', '/admin/access-log/portal', [PortalAccessLogAdminController::class, 'index']);
    $r->addRoute('GET', '/admin/reports/submissions', [ReportsAdminController::class, 'submissionsReport']);
    $r->addRoute('GET', '/admin/reports/submissions/export', [ReportsAdminController::class, 'submissionsReportExportCsv']);
    // Link manual de conta Microsoft para admin
    $r->addRoute('GET',  '/admin/ms-link', [\App\Presentation\Controller\Admin\AdminMicrosoftLinkController::class, 'showForm']);
    $r->addRoute('POST', '/admin/ms-link', [\App\Presentation\Controller\Admin\AdminMicrosoftLinkController::class, 'store']);
    $r->addRoute('GET',  '/admin/documents',              [DocumentAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/documents/new',          [DocumentAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/documents',              [DocumentAdminController::class, 'create']);
    $r->addRoute('GET',  '/admin/documents/{id:\d+}',     [DocumentAdminController::class, 'show']);
    $r->addRoute('POST', '/admin/documents/{id:\d+}/delete', [DocumentAdminController::class, 'delete']);
    // Microsoft OAuth (Admin)
    $r->addRoute('GET', '/admin/login/microsoft', [AdminAuthController::class, 'loginWithMicrosoft']);
    // Callback principal conforme .env MS_ADMIN_REDIRECT_URI
    $r->addRoute('GET', '/admin/auth/callback',   [AdminAuthController::class, 'loginCallback']);
    // Alias opcional para compatibilidade
    $r->addRoute('GET', '/admin/login/callback',  [AdminAuthController::class, 'loginCallback']);
    $r->addRoute('GET',  '/admin/settings/branding',        [SettingsController::class, 'brandingForm']);
    $r->addRoute('POST', '/admin/settings/branding/save',   [SettingsController::class, 'saveBranding']);
    $r->addRoute('GET',  '/admin/announcements',             [AnnouncementAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/announcements/new',         [AnnouncementAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/announcements',             [AnnouncementAdminController::class, 'store']);
    $r->addRoute('GET',  '/admin/announcements/{id:\d+}/edit', [AnnouncementAdminController::class, 'editForm']);
    $r->addRoute('POST', '/admin/announcements/{id:\d+}',    [AnnouncementAdminController::class, 'update']);
    $r->addRoute('POST', '/admin/announcements/{id:\d+}/delete', [AnnouncementAdminController::class, 'delete']);
    // Usuários Finais
    $r->addRoute('GET',  '/admin/portal-users',                    [PortalUserController::class, 'index']);
    $r->addRoute('GET',  '/admin/portal-users/create',             [PortalUserController::class, 'showCreateForm']);
    $r->addRoute('POST', '/admin/portal-users',                    [PortalUserController::class, 'store']);
    $r->addRoute('GET',  '/admin/portal-users/{id:\d+}/edit',      [PortalUserController::class, 'showEditForm']);
    $r->addRoute('GET',  '/admin/portal-users/{id:\d+}', [PortalUserController::class, 'show']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}',           [PortalUserController::class, 'update']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}/deactivate', [PortalUserController::class, 'deactivate']);
    $r->addRoute('POST', '/admin/portal-users/{id:\d+}/tokens', [PortalUserController::class, 'generateAccessCode']);
    // Categorias
    $r->addRoute('GET',  '/admin/document-categories',               [DocumentCategoryAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/document-categories/new',           [DocumentCategoryAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/document-categories',               [DocumentCategoryAdminController::class, 'store']);
    $r->addRoute('GET',  '/admin/document-categories/{id:\d+}/edit', [DocumentCategoryAdminController::class, 'editForm']);
    $r->addRoute('POST', '/admin/document-categories/{id:\d+}',      [DocumentCategoryAdminController::class, 'update']);
    $r->addRoute('POST', '/admin/document-categories/{id:\d+}/delete', [DocumentCategoryAdminController::class, 'delete']);
    // Documentos gerais
    $r->addRoute('GET',  '/admin/general-documents',               [GeneralDocumentAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/general-documents/new',           [GeneralDocumentAdminController::class, 'createForm']);
    $r->addRoute('POST', '/admin/general-documents',               [GeneralDocumentAdminController::class, 'store']);
    $r->addRoute('GET',  '/admin/general-documents/{id:\d+}/edit', [GeneralDocumentAdminController::class, 'editForm']);
    $r->addRoute('POST', '/admin/general-documents/{id:\d+}',      [GeneralDocumentAdminController::class, 'update']);
    $r->addRoute('POST', '/admin/general-documents/{id:\d+}/toggle', [GeneralDocumentAdminController::class, 'toggle']);
    $r->addRoute('POST', '/admin/general-documents/{id:\d+}/delete', [GeneralDocumentAdminController::class, 'delete']);

    // Outbox de notificações
    $r->addRoute('GET',  '/admin/notifications/outbox', [NotificationOutboxAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/notifications/outbox/{id:\d+}',           [NotificationOutboxAdminController::class, 'show']);
    $r->addRoute('POST', '/admin/notifications/outbox/{id:\d+}/cancel',    [NotificationOutboxAdminController::class, 'cancel']);
    $r->addRoute('POST', '/admin/notifications/outbox/{id:\d+}/reprocess', [NotificationOutboxAdminController::class, 'reprocess']);
    $r->addRoute('POST', '/admin/notifications/outbox/{id:\d+}/reset',     [NotificationOutboxAdminController::class, 'resetAndReprocess']);

    // Monitoramento avançado (requisições HTTP, alertas, performance)
    $r->addRoute('GET',  '/admin/monitoring',                    [MonitoringAdminController::class, 'index']);
    $r->addRoute('GET',  '/admin/monitoring/api/stats',          [MonitoringAdminController::class, 'apiStats']);
    $r->addRoute('GET',  '/admin/monitoring/api/alerts',         [MonitoringAdminController::class, 'apiAlerts']);
    $r->addRoute('GET',  '/admin/monitoring/api/requests',       [MonitoringAdminController::class, 'apiRequests']);

    // Global Search
    $r->addRoute('GET', '/admin/search', [\App\Presentation\Controller\Admin\SearchController::class, 'showResults']);
    $r->addRoute('GET', '/admin/api/search', [\App\Presentation\Controller\Admin\SearchController::class, 'search']);
    $r->addRoute('GET', '/admin/api/search/quick', [\App\Presentation\Controller\Admin\SearchController::class, 'quickSearch']);

    // In-App Notifications
    $r->addRoute('GET',  '/admin/api/notifications', [\App\Presentation\Controller\Admin\InAppNotificationController::class, 'getUnread']);
    $r->addRoute('POST', '/admin/api/notifications/{id:\d+}/read', [\App\Presentation\Controller\Admin\InAppNotificationController::class, 'markAsRead']);
    $r->addRoute('POST', '/admin/api/notifications/read-all', [\App\Presentation\Controller\Admin\InAppNotificationController::class, 'markAllAsRead']);

    // (Rotas antigas com AdminMicrosoftAuthController removidas em favor de AdminAuthController)

    // Downloads de arquivos de submissão
    $r->addRoute('GET', '/admin/files/{id:\d+}/download', [FileAdminController::class, 'download']);
    $r->addRoute('GET', '/admin/files/{id:\d+}/preview', [FileAdminController::class, 'preview']);

    // Auditoria
    $r->addRoute('GET', '/admin/audit-logs', [AuditLogController::class, 'index']);
    // Alias amigável
    $r->addRoute('GET', '/admin/audit', [AuditLogController::class, 'index']);

    // Dashboard (rota raiz)
    $r->addRoute('GET', '/admin', [DashboardAdminController::class, 'index']);
});


// Descobre método e URI
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri        = $_SERVER['REQUEST_URI']    ?? '/';

// Remove query string ANTES de qualquer validação de rota
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Inicializa Request Logger
$requestLogger = $config['request_logger'] ?? null;

// --------------------
// Proteção de rotas admin
// --------------------
$publicRoutes = [
    ['GET',  '/admin/login'],
    ['POST', '/admin/login'],
    ['GET',  '/admin/login/microsoft'],
    ['GET',  '/admin/auth/callback'],
    // Password recovery
    ['GET',  '/admin/forgot-password'],
    ['POST', '/admin/forgot-password'],
    // 2FA verification (during login flow)
    ['GET',  '/admin/2fa/verify'],
    ['POST', '/admin/2fa/verify'],
];

// Also allow reset-password with dynamic token
$isPublic = false;
foreach ($publicRoutes as [$method, $path]) {
    if ($httpMethod === $method && $uri === $path) {
        $isPublic = true;
        break;
    }
}

// Allow reset-password with token
if (!$isPublic && preg_match('#^/admin/reset-password/[a-f0-9]+$#', $uri)) {
    $isPublic = true;
}

// Se não for rota pública e não tiver admin logado, redireciona
if (str_starts_with($uri, '/admin') && !$isPublic && !Session::has('admin')) {
    if ($requestLogger) {
        $requestLogger->logUnauthorized(401, 'Not authenticated');
    }
    header('Location: /admin/login');
    exit;
}

// Faz o dispatch
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Renderiza 404
        if ($requestLogger) {
            $requestLogger->logSuccess(404);
        }
        $viewData = []; // No data needed
        require __DIR__ . '/../src/Presentation/View/errors/404.php';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        // Renderiza 404 (ou 405 se preferir)
        if ($requestLogger) {
            $requestLogger->logError('Method not allowed', 405);
        }
        $viewData = [];
        require __DIR__ . '/../src/Presentation/View/errors/404.php';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars    = $routeInfo[2];

        try {
            // 1) Se o handler for um array [Controller::class, 'method']
            if (is_array($handler) && isset($handler[0], $handler[1])) {
                [$class, $method] = $handler;

                $controller = new $class($config);
                $response   = $controller->$method($vars);

                if (is_string($response)) {
                    echo $response;
                }
                
                // Log sucesso
                $statusCode = http_response_code();
                if ($requestLogger) {
                    $requestLogger->logSuccess($statusCode);
                }
                break;
            }

            // 2) Se for uma Closure / callable simples
            if (is_callable($handler)) {
                echo $handler(...array_values($vars));
                $statusCode = http_response_code();
                if ($requestLogger) {
                    $requestLogger->logSuccess($statusCode);
                }
                break;
            }

            // 3) Fallback (não deveria chegar aqui)
            http_response_code(500);
            if ($requestLogger) {
                $requestLogger->logError('Invalid handler', 500);
            }
            $error = 'Invalid route handler';
            require __DIR__ . '/../src/Presentation/View/errors/500.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            if ($requestLogger) {
                $requestLogger->logError($e->getMessage(), 500, $e);
            }
            // Passa o erro para a view
            $error = $e->getMessage();
            if ($_SERVER['APP_ENV'] ?? 'production' !== 'production') {
                 $error .= "\n\n" . $e->getTraceAsString();
            }
            require __DIR__ . '/../src/Presentation/View/errors/500.php';
        }
        break;
}
