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
use App\Infrastructure\Logging\RequestLogger;
use App\Support\Translator;
use App\Infrastructure\ErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Inicializa Tradutor
Translator::init(__DIR__ . '/../resources/lang', $_ENV['APP_LOCALE'] ?? 'pt-BR');

// Sessão - configure cookies properly for HTTPS
$appUrl = $_ENV['APP_URL'] ?? '';
$appUrlParts = parse_url($appUrl);
$appUrlHost = $appUrlParts['host'] ?? '';

// Detect if current request is actually HTTPS (not just APP_URL config)
// This allows the same code to work in both HTTP local dev and HTTPS production
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

$sessionName = $_ENV['SESSION_NAME'] ?? 'nimbusdocs_session';
session_name($sessionName);

// Set cookie params before starting session
if (session_status() !== PHP_SESSION_ACTIVE) {
    $cookieParams = [
        'lifetime' => 0, // Session cookie (until browser closes)
        'path' => '/',
        'domain' => (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== $appUrlHost) ? null : ($appUrlHost ?: null),
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Strict',
    ];

    session_set_cookie_params($cookieParams);
    session_start();

    // Garantir que o cookie de sessão seja enviado já na primeira resposta
    if (!headers_sent()) {
        setcookie(
            $sessionName,
            session_id(),
            [
                'expires' => 0,
                'path' => $cookieParams['path'],
                'domain' => $cookieParams['domain'],
                'secure' => $cookieParams['secure'],
                'httponly' => $cookieParams['httponly'],
                'samesite' => $cookieParams['samesite'],
            ]
        );
    }
}

// -------------------------------------------------------------------------
// Security Headers
// -------------------------------------------------------------------------
if (!headers_sent()) {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), camera=(), microphone=()");
    
    // CSP: Allow 'self', data: images, and unsafe-inline for styles/scripts (compatibility)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self';");

    if ($isHttps) {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }
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

// Handler global de erros
$errorHandler = new ErrorHandler(
    __DIR__ . '/../src/Presentation/View/errors',
    $appDebug
);

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
// File Cache (cache baseado em arquivos)
// -------------------------------------------------------------------------
$cacheDir = __DIR__ . '/../storage/cache';
$fileCache = new \App\Support\FileCache($cacheDir, 86400); // 24h default TTL
$config['cache'] = $fileCache;

// -------------------------------------------------------------------------
// CNPJ Service com Cache
// -------------------------------------------------------------------------
$cnpjWsService = new \App\Infrastructure\Integration\CnpjWsService($logger);
$cachedCnpjService = new \App\Infrastructure\Integration\CachedCnpjService(
    $cnpjWsService,
    $fileCache,
    $logger,
    604800 // 7 dias de cache para dados de CNPJ
);
$config['cnpj_service'] = $cachedCnpjService;

// -------------------------------------------------------------------------
// Request Logger (logging avançado de requisições HTTP)
// -------------------------------------------------------------------------
$requestLogger = new RequestLogger($logger);
$config['request_logger'] = $requestLogger;


// -------------------------------------------------------------------------
// Serviço de e-mail via Microsoft Graph
// -------------------------------------------------------------------------
// Passa explicitamente as credenciais do .env para o serviço
$graphMailCfg = [
    'GRAPH_TENANT_ID'    => $_ENV['GRAPH_TENANT_ID']    ?? '',
    'GRAPH_CLIENT_ID'    => $_ENV['GRAPH_CLIENT_ID']    ?? '',
    'GRAPH_CLIENT_SECRET' => $_ENV['GRAPH_CLIENT_SECRET'] ?? '',
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
    'admin_logo_url' => $settings['branding.admin_logo_url'] ?? '',
    'portal_logo_url' => $settings['branding.portal_logo_url'] ?? '',
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
$outboxRepo = new MySqlNotificationOutboxRepository($pdo, $logger);

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
