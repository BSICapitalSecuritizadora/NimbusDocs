<?php

declare(strict_types=1);

namespace App\Support;

final class FileUpload
{
    /** @var string[] */
    private static array $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'jpg', 'jpeg', 'png'];

    /** @var string[] */
    private static array $blockedExtensions = [
        'docm', 'xlsm', 'pptm', 'dotm', 'xltm', 'potm', // Office com macros
        'php', 'phtml', 'php3', 'php4', 'php5', 'phar', // Executáveis PHP
        'exe', 'com', 'bat', 'cmd', 'sh', 'ps1',        // Executáveis sistema
        'jar', 'jsp', 'asp', 'aspx', 'cgi',             // Executáveis web
        'htaccess', 'htpasswd', 'config', 'ini',        // Configs
    ];

    /** @var string[] */
    private static array $allowedMimePrefix = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument',
        'application/vnd.ms-excel',
        'image/jpeg',
        'image/png',
        'text/csv',
    ];

    /**
     * @return array{path:string, original_name:string, size:int, mime_type:string}
     */
    public static function store(array $file, string $baseDir): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \RuntimeException('Upload inválido.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Falha no upload (código ' . $file['error'] . ').');
        }

        $original = $file['name'] ?? 'arquivo';
        
        // Validação de tamanho do nome
        if (strlen($original) > 255) {
            throw new \RuntimeException('Nome do arquivo muito longo (máximo 255 caracteres).');
        }

        $size = (int)$file['size'];

        $maxMb  = (int)(getenv('MAX_UPLOAD_MB') ?: 100);
        $maxB   = $maxMb * 1024 * 1024;
        if ($size > $maxB) {
            throw new \RuntimeException('Arquivo excede o limite de ' . $maxMb . 'MB.');
        }

        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        // Proteção contra double extension (ex: arquivo.pdf.php)
        $fullBaseName = pathinfo($original, PATHINFO_FILENAME);
        if (preg_match('/\.(' . implode('|', self::$blockedExtensions) . ')$/i', $fullBaseName)) {
            throw new \RuntimeException('Nome de arquivo contém extensão perigosa.');
        }

        // Bloquear extensões perigosas
        if (in_array($ext, self::$blockedExtensions, true)) {
            throw new \RuntimeException('Tipo de arquivo não permitido (bloqueado).');
        }

        if (!in_array($ext, self::$allowedExtensions, true)) {
            throw new \RuntimeException('Tipo de arquivo não permitido (extensão).');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';

        $isAllowedMime = false;
        foreach (self::$allowedMimePrefix as $prefix) {
            if (str_starts_with($mime, $prefix)) {
                $isAllowedMime = true;
                break;
            }
        }
        if (!$isAllowedMime) {
            throw new \RuntimeException('Tipo de arquivo não permitido (MIME).');
        }

        // Gera nome aleatório seguro
        $randomName = bin2hex(random_bytes(16)) . '.' . $ext;

        // Garante diretório
        if (!is_dir($baseDir) && !mkdir($baseDir, 0775, true) && !is_dir($baseDir)) {
            throw new \RuntimeException('Não foi possível criar diretório de armazenamento.');
        }

        $target = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $randomName;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Falha ao salvar arquivo no servidor.');
        }

        return [
            'path'          => $target,
            'original_name' => $original,
            'size'          => $size,
            'mime_type'     => $mime,
        ];
    }
}
