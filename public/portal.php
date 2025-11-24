<?php

declare(strict_types=1);

use App\Presentation\Controller\Portal\Auth\PortalLoginController;
use App\Support\Session;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$config = require __DIR__ . '/../bootstrap/app.php';

// Router do portal
$dispatcher = simpleDispatcher(function (RouteCollector $r): void {
    // Login portal
    $r->addRoute('GET',  '/portal/login',  [PortalLoginController::class, 'showLoginForm']);
    $r->addRoute('POST', '/portal/login',  [PortalLoginController::class, 'handleLogin']);
    $r->addRoute('GET',  '/portal/logout', [PortalLoginController::class, 'logout']);

    // Dashboard do usuário final
    $r->addRoute('GET', '/portal', function () {
        echo '<div style="font-family:system-ui;padding:2rem">
                <h2>Portal do Usuário</h2>
                <p>Login efetuado com sucesso. Aqui vai entrar o envio de informações e documentos.</p>
              </div>';
    });
});

// Método e URI
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri        = $_SERVER['REQUEST_URI']    ?? '/';

// Remove query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

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
    header('Location: /portal/login');
    exit;
}

// ------------- Dispatch -------------
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

        if (is_array($handler) && isset($handler[0], $handler[1])) {
            [$class, $method] = $handler;

            $controller = new $class($config);
            $response   = $controller->$method($vars);

            if (is_string($response)) {
                echo $response;
            }
            break;
        }

        if (is_callable($handler)) {
            echo $handler(...array_values($vars));
            break;
        }

        http_response_code(500);
        echo '500 - Handler inválido';
        break;
}
