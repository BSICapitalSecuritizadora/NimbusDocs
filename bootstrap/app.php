<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Infrastructure\Persistence\Connection;
use App\Infrastructure\Notification\GraphMailService;
use App\Application\Service\NotificationService;
use App\Infrastructure\Logging\AdminAuditLogger;
use App\Domain\Repository\AuditLogRepository;
use App\Infrastructure\Persistence\MySqlAuditLogRepository;
use App\Infrastructure\Audit\AuditLogger;
use App\Infrastructure\Persistence\MySqlSettingsRepository;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;
use App\Infrastructure\Auth\AzureAdminAuthClient;
use App\Infrastructure\Logging\PortalAccessLogger;


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
// Portal Access Logger
// -------------------------------------------------------------------------
$portalAccessLogger = new PortalAccessLogger($pdo);
$config['portal_access_logger'] = $portalAccessLogger;

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

// Adiciona logger ao config
$config['logger'] = $logger;

// -------------------------------------------------------------------------
// Serviço de e-mail via Microsoft Graph
// -------------------------------------------------------------------------
// Passa explicitamente as credenciais do .env para o serviço
$graphMailCfg = [
    'GRAPH_TENANT_ID'    => $_ENV['GRAPH_TENANT_ID']    ?? '',
    'GRAPH_CLIENT_ID'    => $_ENV['GRAPH_CLIENT_ID']    ?? '',
    'GRAPH_CLIENT_SECRET'=> $_ENV['GRAPH_CLIENT_SECRET']?? '',
    'MAIL_FROM'          => $_ENV['MAIL_FROM']          ?? '',
    'MAIL_FROM_NAME'     => $_ENV['MAIL_FROM_NAME']     ?? 'NimbusDocs',
];

$config['mail'] = new GraphMailService($graphMailCfg, $logger);

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
// Configurações de notificações
// -------------------------------------------------------------------------
$config['settings'] = $settings;

// -------------------------------------------------------------------------
// Notification Service (serviço de notificações por e-mail)
// -------------------------------------------------------------------------
// Disponibiliza um serviço central de notificações para os controllers.
$adminUserRepo = new MySqlAdminUserRepository($pdo);
$portalUserRepo = new MySqlPortalUserRepository($pdo);

$graphMailConfig = [
    'GRAPH_TENANT_ID'      => $_ENV['GRAPH_TENANT_ID'] ?? '',
    'GRAPH_CLIENT_ID'      => $_ENV['GRAPH_CLIENT_ID'] ?? '',
    'GRAPH_CLIENT_SECRET'  => $_ENV['GRAPH_CLIENT_SECRET'] ?? '',
    'MAIL_FROM'            => $_ENV['GRAPH_SENDER_EMAIL'] ?? '',
    'MAIL_FROM_NAME'       => 'NimbusDocs'
];

$mailLogger = new Logger('mail');
$mailLogger->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/mail.log', Logger::DEBUG));
$graphMailService = new GraphMailService($graphMailConfig, $mailLogger);
// Outbox repository para fila de notificações
$outboxRepo = new MySqlNotificationOutboxRepository($pdo);

$notificationService = new NotificationService(
    $graphMailService,
    $settingsRepo,
    $adminUserRepo,
    $portalUserRepo,
    $outboxRepo
);

$config['notification'] = $notificationService;
$config['admin_user_repo'] = $adminUserRepo;
$config['portal_user_repo'] = $portalUserRepo;

// -------------------------------------------------------------------------
// Azure Admin OAuth Client (TheNetworg OAuth2 Azure)
// -------------------------------------------------------------------------
$azureClient = new AzureAdminAuthClient($config['ms_admin_auth']);
$config['azure_admin_auth'] = $azureClient;

return $config;
