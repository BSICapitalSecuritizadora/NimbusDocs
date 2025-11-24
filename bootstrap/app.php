<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use App\Infrastructure\Persistence\Connection;

require __DIR__ . '/../vendor/autoload.php';

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Sessão
session_name($_ENV['SESSION_NAME'] ?? 'nimbusdocs_session');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Debug / erros
$appDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);

if ($appDebug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
}

// 1) Carrega config principal (AGORA criamos $config)
$config = require __DIR__ . '/../config/config.php';

// 2) Cria conexão PDO usando os dados de $config['db']
$pdo = Connection::make($config['db']);

// 3) Injeta PDO dentro do array de config para uso pelos controllers
$config['pdo'] = $pdo;

return $config;
