<?php

declare(strict_types=1);

/**
 * Middleware de Request Logging
 * Integra-se no public/admin.php e public/portal.php para logar todas as requisições
 * 
 * Use assim no final do dispatch:
 * 
 * // Log requisição bem-sucedida
 * $requestLogger->logSuccess($statusCode);
 * 
 * // Log erro
 * $requestLogger->logError($error, $statusCode, $exception);
 * 
 * // Log acesso negado
 * $requestLogger->logUnauthorized($statusCode, $reason);
 */

// Exemplo de integração completa:
// Adicionar após o dispatch no admin.php e portal.php

use App\Infrastructure\Logging\RequestLogger;

// Capture o objeto logger do config e cria RequestLogger
if (isset($config['logger'])) {
    $requestLogger = new RequestLogger($config['logger']);
} else {
    // Fallback se logger não estiver configurado
    $requestLogger = null;
}

// Depois, no final do dispatch, use:
// $requestLogger->logSuccess(http_response_code());
// $requestLogger->logError('Erro', 500, $exception);
// $requestLogger->logUnauthorized(403, 'Token inválido');
