<?php

declare(strict_types=1);

/**
 * Script de Limpeza de Dados e Arquivos (Política de Retenção / LGPD).
 *
 * Executar via Cron:
 * 0 3 * * 0 /usr/bin/php /path/to/NimbusDocs/bin/cleanup.php
 * (Rodar semanalmente, ex: Domingo às 03:00)
 */

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../bootstrap/app.php';
$pdo = $config['pdo'];

use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;

echo '[' . date('Y-m-d H:i:s') . "] --- INICIANDO LIMPEZA DE DADOS (LGPD) ---\n";

try {
    // -------------------------------------------------------------------------
    // 1. Limpeza de Tokens de Acesso Expirados/Revogados
    // -------------------------------------------------------------------------
    $tokenRepo = new MySqlPortalAccessTokenRepository($pdo);
    $daysToken = 30; // 30 dias de retenção após expiração

    echo "1. Limpando tokens expirados/revogados há mais de {$daysToken} dias...\n";
    $deletedTokens = $tokenRepo->deleteExpired($daysToken);
    echo "   -> {$deletedTokens} tokens removidos.\n";

    // -------------------------------------------------------------------------
    // 2. Limpeza de Rascunhos Abandonados
    // -------------------------------------------------------------------------
    $submissionRepo = new MySqlPortalSubmissionRepository($pdo);
    $fileRepo = new MySqlPortalSubmissionFileRepository($pdo);
    $daysDraft = 90; // 90 dias de tolerância para rascunhos

    echo "2. Buscando rascunhos (PENDING) abandonados há mais de {$daysDraft} dias...\n";
    $abandoned = $submissionRepo->findAbandonedDrafts($daysDraft);
    echo '   -> ' . count($abandoned) . " rascunhos encontrados.\n";

    $deletedSubmissions = 0;
    $deletedFiles = 0;
    $freedSpace = 0;

    $baseDir = dirname(__DIR__) . '/storage/'; // Ajuste conforme seu config de storage

    foreach ($abandoned as $sub) {
        $subId = (int) $sub['id'];
        $ref = $sub['reference_code'];

        // a) Buscar arquivos para remover fisicamente
        $files = $fileRepo->findBySubmission($subId);

        foreach ($files as $file) {
            $path = $file['storage_path'];
            // O path no DB pode ser relativo ou absoluto, dependendo de como foi salvo.
            // Assumindo relativo a 'storage/', mas verificando existência.

            $fullPath = $baseDir . $path; // Tenta relativo padrão
            if (!file_exists($fullPath) && file_exists($path)) {
                $fullPath = $path; // Era absoluto
            }

            if (file_exists($fullPath) && is_file($fullPath)) {
                $size = filesize($fullPath);
                if (@unlink($fullPath)) {
                    $deletedFiles++;
                    $freedSpace += $size;
                    // echo "      [ARQUIVO] Removido: $path\n";
                } else {
                    echo "      [ERRO] Falha ao remover arquivo: $path\n";
                }
            }
        }

        // b) Remover registros de arquivos
        $fileRepo->deleteBySubmissionId($subId);

        // c) Remover a submissão
        if ($submissionRepo->delete($subId)) {
            // echo "   [SUBMISSÃO] Removida: $ref (ID: $subId)\n";
            $deletedSubmissions++;
        }
    }

    $freedMb = number_format($freedSpace / 1024 / 1024, 2);
    echo "   -> {$deletedSubmissions} submissões removidas.\n";
    echo "   -> {$deletedFiles} arquivos físicos deletados.\n";
    echo "   -> {$freedMb} MB de espaço liberado.\n";

    // -------------------------------------------------------------------------
    // 3. (Opcional) Limpeza de Logs de Auditoria Antigos (> 5 Anos)
    // -------------------------------------------------------------------------
    // Por precaução, não vamos deletar logs de auditoria automaticamente
    // sem um requisito explicito de prazo (ex: 5 anos).
    // Implementar aqui se necessário: DELETE FROM audit_logs WHERE occurred_at < ...

    echo '[' . date('Y-m-d H:i:s') . "] --- LIMPEZA CONCLUÍDA COM SUCESSO ---\n";

} catch (\Throwable $e) {
    echo '[ERRO CRÍTICO] ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
