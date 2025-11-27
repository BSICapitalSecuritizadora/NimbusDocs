<?php

declare(strict_types=1);

return [
    'app' => [
        'name'      => $_ENV['APP_NAME']    ?? 'NimbusDocs',
        'env'       => $_ENV['APP_ENV']     ?? 'local',
        'debug'     => $_ENV['APP_DEBUG']   ?? 'false',
        'url'       => $_ENV['APP_URL']     ?? 'https://nimbusdocs.local/',
        'timezone'  => $_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo',
        'secret'    => $_ENV['APP_SECRET']  ?? '',
        'session'   => $_ENV['SESSION_NAME'] ?? 'nimbusdocs_session',
    ],

    'db' => [
        'driver'   => $_ENV['DB_CONNECTION'] ?? 'mysql',
        'host'     => $_ENV['DB_HOST']       ?? '127.0.0.1',
        'port'     => (int)($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_DATABASE']   ?? 'nimbusdocs',
        'username' => $_ENV['DB_USERNAME']   ?? 'root',
        'password' => $_ENV['DB_PASSWORD']   ?? '',
        'charset'  => 'utf8mb4',
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],

    'log' => [
        'channel' => $_ENV['LOG_CHANNEL'] ?? 'single',
        'path'    => $_ENV['LOG_PATH']    ?? 'storage/logs/nimbusdocs.log',
        'level'   => $_ENV['LOG_LEVEL']   ?? 'debug',
    ],

    'upload' => [
        // tamanho em MB (apenas informativo / para UI)
        'max_filesize_mb'    => (int)($_ENV['UPLOAD_MAX_FILESIZE_MB'] ?? 100),

        // tamanho em BYTES (para validação no PHP)
        'max_filesize_bytes' => (int) (($_ENV['UPLOAD_MAX_FILESIZE_MB'] ?? 100) * 1024 * 1024),

        // se quiser controlar via .env, ótimo; se ficar vazio, a gente usa um default no controller
        'allowed_mime'       => array_filter(
            array_map('trim', explode(',', $_ENV['UPLOAD_ALLOWED_MIME'] ?? ''))
        ),

        // transforma em caminho ABSOLUTO: <raiz do projeto>/storage/uploads
        'storage_path'       => dirname(__DIR__) . '/' . ltrim(
            $_ENV['UPLOAD_STORAGE_PATH'] ?? 'storage/uploads',
            '/'
        ),
    ],

    'ms_admin_auth' => [
        'tenant_id'      => $_ENV['MS_ADMIN_TENANT_ID']      ?? '',
        'client_id'      => $_ENV['MS_ADMIN_CLIENT_ID']      ?? '',
        'client_secret'  => $_ENV['MS_ADMIN_CLIENT_SECRET']  ?? '',
        'redirect_uri'   => $_ENV['MS_ADMIN_REDIRECT_URI']   ?? '',
    ],
];
