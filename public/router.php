<?php

// Router para o PHP Built-in Web Server (usado pelo Symfony Panther)
// Simula o comportamento do .htaccess

if (php_sapi_name() !== 'cli-server') {
    die('Este script só pode ser rodado via php -S');
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Se for requisicao a um arquivo estatico existente (CSS, JS, Imagens)
$filePath = __DIR__ . $path;
if ($path !== '/' && file_exists($filePath)) {
    return false; // Deixa o servidor interno servir o arquivo
}

// Simulando o mod_rewrite do Apache
if (preg_match('#^/admin/?(.*)$#', $path)) {
    $_SERVER['SCRIPT_NAME'] = '/admin.php';
    require __DIR__ . '/admin.php';
} elseif (preg_match('#^/portal/?(.*)$#', $path)) {
    $_SERVER['SCRIPT_NAME'] = '/portal.php';
    require __DIR__ . '/portal.php';
} elseif (preg_match('#^/api/?(.*)$#', $path)) {
    $_SERVER['SCRIPT_NAME'] = '/api.php';
    require __DIR__ . '/api.php';
} else {
    // Fallback normal
    require __DIR__ . '/index.php';
}
