<?php

declare(strict_types=1);

use App\Presentation\Controller\Admin\Auth\LoginController;
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

    // Logout
    $r->addRoute('GET', '/admin/logout', [LoginController::class, 'logout']);

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

// --------------------
// Proteção de rotas admin
// --------------------
$publicRoutes = [
    ['GET',  '/admin/login'],
    ['POST', '/admin/login'],
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

// Remove query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

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
