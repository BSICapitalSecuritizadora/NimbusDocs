<?php

declare(strict_types=1);

/**
 * NimbusDocs REST API Entry Point
 * 
 * Provides a public API for integrations.
 * Authentication is done via API tokens or JWT.
 */

use App\Presentation\Controller\Api\AuthApiController;
use App\Presentation\Controller\Api\SubmissionsApiController;
use App\Presentation\Controller\Api\UsersApiController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Bootstrap the application
$config = require __DIR__ . '/../bootstrap/app.php';

// CORS headers for API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Token');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// API Router
$dispatcher = simpleDispatcher(function (RouteCollector $r): void {
    // Authentication
    $r->addRoute('POST', '/api/v1/auth/login', [AuthApiController::class, 'login']);
    $r->addRoute('POST', '/api/v1/auth/token', [AuthApiController::class, 'createToken']);
    $r->addRoute('DELETE', '/api/v1/auth/token', [AuthApiController::class, 'revokeToken']);
    
    // Submissions
    $r->addRoute('GET', '/api/v1/submissions', [SubmissionsApiController::class, 'list']);
    $r->addRoute('GET', '/api/v1/submissions/{id:\d+}', [SubmissionsApiController::class, 'show']);
    $r->addRoute('POST', '/api/v1/submissions', [SubmissionsApiController::class, 'create']);
    $r->addRoute('PUT', '/api/v1/submissions/{id:\d+}/status', [SubmissionsApiController::class, 'updateStatus']);
    
    // Users
    $r->addRoute('GET', '/api/v1/users', [UsersApiController::class, 'list']);
    $r->addRoute('GET', '/api/v1/users/{id:\d+}', [UsersApiController::class, 'show']);
    $r->addRoute('POST', '/api/v1/users', [UsersApiController::class, 'create']);
    $r->addRoute('PUT', '/api/v1/users/{id:\d+}', [UsersApiController::class, 'update']);
    
    // Health check
    $r->addRoute('GET', '/api/v1/health', function() {
        return json_encode([
            'status' => 'ok',
            'version' => '1.0.0',
            'timestamp' => date('c'),
        ]);
    });
});

// Get request info
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode([
            'error' => 'Not Found',
            'message' => 'The requested endpoint does not exist.',
        ]);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode([
            'error' => 'Method Not Allowed',
            'message' => 'The requested method is not allowed for this endpoint.',
            'allowed' => $routeInfo[1],
        ]);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        try {
            if (is_array($handler) && isset($handler[0], $handler[1])) {
                [$class, $method] = $handler;
                $controller = new $class($config);
                $response = $controller->$method($vars);
                
                if (is_string($response)) {
                    echo $response;
                } elseif (is_array($response)) {
                    echo json_encode($response);
                }
            } elseif (is_callable($handler)) {
                $result = $handler(...array_values($vars));
                if (is_string($result)) {
                    echo $result;
                } elseif (is_array($result)) {
                    echo json_encode($result);
                }
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => $config['app']['debug'] ?? false 
                    ? $e->getMessage() 
                    : 'An unexpected error occurred.',
            ]);
            
            if (isset($config['logger'])) {
                $config['logger']->error('API Error', [
                    'uri' => $uri,
                    'method' => $httpMethod,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        break;
}
