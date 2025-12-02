<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Infrastructure\Persistence\Connection;
use App\Infrastructure\Notification\GraphMailService;
use App\Infrastructure\Notification\NotificationService;
use App\Infrastructure\Logging\AdminAuditLogger;
use App\Domain\Repository\AuditLogRepository;
use App\Infrastructure\Persistence\MySqlAuditLogRepository;
use App\Infrastructure\Audit\AuditLogger;
use App\Infrastructure\Persistence\MySqlSettingsRepository;
use App\Infrastructure\Auth\AzureAdminAuthClient;


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

// -------------------------------------------------------------------------
// Logger (Monolog)
// -------------------------------------------------------------------------
$logDir = __DIR__ . '/../storage/logs';

if (!is_dir($logDir)) {
    mkdir($logDir, 0775, true);
}

$logger = new Logger('nimbusdocs');
$logger->pushHandler(
    new StreamHandler($logDir . '/app.log', Logger::DEBUG)
);

// -------------------------------------------------------------------------
// Serviço de e-mail via Microsoft Graph
// -------------------------------------------------------------------------
$config['mail'] = new GraphMailService(
    $config,   // passa o array de config completo
    $logger
);

// -------------------------------------------------------------------------
// Audit Logger (ações administrativas)
// -------------------------------------------------------------------------
$config['audit'] = new AdminAuditLogger($config['pdo']);

// -------------------------------------------------------------------------
// Audit Logger (ações do portal)
// -------------------------------------------------------------------------
$auditRepo = new MySqlAuditLogRepository($pdo);
$auditLogger = new AuditLogger($auditRepo, $config);

// Sobrescreve a chave 'audit' do config com o novo logger
$config['audit'] = $auditLogger;

// -------------------------------------------------------------------------
// Settings Repository (repositório de configurações)
// -------------------------------------------------------------------------
$config['settings_repo'] = new MySqlSettingsRepository($pdo);
// Carrega configurações persistidas e monta branding acessível aos controllers
$settingsRepo = $config['settings_repo'];
$settings = method_exists($settingsRepo, 'getAll') ? $settingsRepo->getAll() : [];

$branding = [
    'app_name'      => $settings['app.name'] ?? ($config['app']['name'] ?? 'NimbusDocs'),
    'app_subtitle'  => $settings['app.subtitle'] ?? 'Portal de documentos',
    'primary_color' => $settings['branding.primary_color'] ?? '#00205b',
    'accent_color'  => $settings['branding.accent_color'] ?? '#ffc20e',
    'admin_logo_url'=> $settings['branding.admin_logo_url'] ?? '',
    'portal_logo_url'=> $settings['branding.portal_logo_url'] ?? '',
];

$config['branding'] = $branding;

// -------------------------------------------------------------------------
// Notification Service (serviço de notificações por e-mail)
// -------------------------------------------------------------------------
// Disponibiliza um serviço central de notificações para os controllers.
$notificationService = new NotificationService($config['mail'], $config);
$config['notifications_service'] = $notificationService;

// -------------------------------------------------------------------------
// Azure Admin OAuth Client (TheNetworg OAuth2 Azure)
// -------------------------------------------------------------------------
$azureClient = new AzureAdminAuthClient($config['ms_admin_auth']);
$config['azure_admin_auth'] = $azureClient;

return $config;
