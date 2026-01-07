<?php

declare(strict_types=1);

use App\Presentation\Controller\Portal\Auth\PortalLoginController;
use App\Presentation\Controller\Portal\PortalSubmissionController;
use App\Presentation\Controller\Portal\PortalFileController;
use App\Presentation\Controller\Portal\PortalHomeController;
use App\Presentation\Controller\Portal\PortalDocumentController;
use App\Presentation\Controller\Portal\PortalGeneralDocumentsController;
use App\Presentation\Controller\Portal\PortalGeneralDocumentsViewerController;
use App\Presentation\Controller\Portal\PortalAnnouncementController;
use App\Presentation\Controller\Portal\PortalProfileController;
use App\Infrastructure\Logging\RequestLogger;
use App\Support\Session;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$config = require __DIR__ . '/../bootstrap/app.php';

// Router do portal
$dispatcher = simpleDispatcher(function (RouteCollector $r): void {
    // Login portal (já existe)
    $r->addRoute('GET',  '/portal/login',  [PortalLoginController::class, 'showLoginForm']);
    $r->addRoute('POST', '/portal/login',  [PortalLoginController::class, 'handleLogin']);
    $r->addRoute('GET',  '/portal/logout', [PortalLoginController::class, 'logout']);

    // Perfil do usuário
    $r->addRoute('GET',  '/portal/profile', [PortalProfileController::class, 'edit']);
    $r->addRoute('POST', '/portal/profile', [PortalProfileController::class, 'update']);

    // Submissões do usuário final
    $r->addRoute('GET',  '/portal/submissions',                [PortalSubmissionController::class, 'index']);
    $r->addRoute('GET',  '/portal/submissions/create',         [PortalSubmissionController::class, 'showCreateForm']);
    $r->addRoute('GET',  '/portal/submissions/new',            [PortalSubmissionController::class, 'showCreateForm']);
    $r->addRoute('POST', '/portal/submissions',                [PortalSubmissionController::class, 'store']);
    $r->addRoute('GET',  '/portal/submissions/{id:\d+}',       [PortalSubmissionController::class, 'show']);
    $r->addRoute('POST', '/portal/api/cnpj',                   [PortalSubmissionController::class, 'getCnpjData']);

    // Documentos do usuário final
    $r->addRoute('GET', '/portal/documents',          [PortalDocumentController::class, 'index']);
    $r->addRoute('GET', '/portal/documents/{id:\d+}', [PortalDocumentController::class, 'download']);

    // Downloads de arquivos de submissão pelo usuário final
    $r->addRoute('GET', '/portal/files/{id:\d+}/download', [PortalFileController::class, 'download']);

    // Dashboard/Home do portal
    $r->addRoute('GET', '/portal', [PortalHomeController::class, 'index']);

    // Documentos gerais do portal
    $r->addRoute('GET', '/portal/documents/general',              [PortalGeneralDocumentsController::class, 'index']);
    $r->addRoute('GET', '/portal/documents/general/{id:\d+}',     [PortalGeneralDocumentsController::class, 'download']);
    $r->addRoute('GET', '/portal/documents/general/{id:\d+}/view',   [PortalGeneralDocumentsViewerController::class, 'view']);
    $r->addRoute('GET', '/portal/documents/general/{id:\d+}/stream', [PortalGeneralDocumentsViewerController::class, 'stream']);

    // Comunicados do portal
    $r->addRoute('GET', '/portal/announcements',     [PortalAnnouncementController::class, 'index']);
    $r->addRoute('GET', '/portal/announcements/{id:\d+}', [PortalAnnouncementController::class, 'show']);
});


// Método e URI
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri        = $_SERVER['REQUEST_URI']    ?? '/';

// Remove query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Inicializa Request Logger
$requestLogger = $config['request_logger'] ?? null;

// ------------- Proteção de rotas do portal -------------
$publicRoutes = [
    ['GET',  '/portal/login'],
    ['POST', '/portal/login'],
];

$isPublic = false;
foreach ($publicRoutes as [$method, $path]) {
    if ($httpMethod === $method && $uri === $path) {
        $isPublic = true;
        break;
    }
}

// Se não for rota pública e for do /portal, exige usuário logado
if (str_starts_with($uri, '/portal') && !$isPublic && !Session::has('portal_user')) {
    if ($requestLogger) {
        $requestLogger->logUnauthorized(401, 'Portal user not authenticated');
    }
    header('Location: /portal/login');
    exit;
}

// ------------- Dispatch -------------
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        if ($requestLogger) {
            $requestLogger->logSuccess(404);
        }
        echo '404 - Página não encontrada';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        if ($requestLogger) {
            $requestLogger->logError('Method not allowed', 405);
        }
        echo '405 - Método não permitido';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars    = $routeInfo[2];

        try {
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

            if (is_callable($handler)) {
                echo $handler(...array_values($vars));
                $statusCode = http_response_code();
                if ($requestLogger) {
                    $requestLogger->logSuccess($statusCode);
                }
                break;
            }

            http_response_code(500);
            if ($requestLogger) {
                $requestLogger->logError('Invalid handler', 500);
            }
            echo '500 - Handler inválido';
        } catch (\Throwable $e) {
            http_response_code(500);
            if ($requestLogger) {
                $requestLogger->logError($e->getMessage(), 500, $e);
            }
            throw $e;
        }
        break;
}
