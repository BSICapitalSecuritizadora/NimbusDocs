<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Helper para download de arquivos com streaming verdadeiro.
 * Evita estourar memória ao enviar arquivos grandes em chunks.
 */
final class StreamingFileDownloader
{
    /**
     * Tamanho do chunk em bytes (8KB).
     * Valor otimizado para maioria dos cenários.
     */
    private const CHUNK_SIZE = 8192;

    /**
     * Faz streaming de um arquivo para o cliente.
     *
     * @param string $filePath Caminho absoluto do arquivo
     * @param string $mimeType MIME type do arquivo
     * @param string $fileName Nome do arquivo para o cliente
     * @param string $disposition 'attachment' para download, 'inline' para preview
     * @param int|null $fileSize Tamanho do arquivo em bytes (opcional, será calculado se não informado)
     * @return bool True se o streaming foi completado com sucesso
     */
    public function stream(
        string $filePath,
        string $mimeType,
        string $fileName,
        string $disposition = 'attachment',
        ?int $fileSize = null
    ): bool {
        // Valida existência do arquivo
        if (!is_file($filePath) || !is_readable($filePath)) {
            return false;
        }

        // Calcula tamanho se não informado
        $size = $fileSize ?? filesize($filePath);
        if ($size === false) {
            $size = 0;
        }

        // Sanitiza nome do arquivo para header
        $safeFileName = $this->sanitizeFileName($fileName);

        // Limpa qualquer output buffer existente
        $this->clearOutputBuffers();

        // Envia headers
        $this->sendHeaders($mimeType, $safeFileName, $disposition, $size);

        // Abre o arquivo para leitura binária
        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            return false;
        }

        // Faz streaming em chunks
        $this->streamChunks($handle);

        fclose($handle);

        return true;
    }

    /**
     * Limpa todos os output buffers ativos.
     */
    private function clearOutputBuffers(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    /**
     * Envia os headers HTTP apropriados para download.
     */
    private function sendHeaders(
        string $mimeType,
        string $fileName,
        string $disposition,
        int $size
    ): void {
        // Previne cache do navegador para arquivos sensíveis
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Headers de download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: ' . $disposition . '; filename="' . $fileName . '"');
        header('Content-Length: ' . $size);
        header('Content-Transfer-Encoding: binary');

        // Desabilita timeout para arquivos grandes
        set_time_limit(0);
    }

    /**
     * Faz streaming do arquivo em chunks.
     *
     * @param resource $handle Handle do arquivo aberto
     */
    private function streamChunks($handle): void
    {
        // Verifica se a conexão ainda está ativa
        while (!feof($handle) && connection_status() === CONNECTION_NORMAL) {
            // Lê chunk do arquivo
            $chunk = fread($handle, self::CHUNK_SIZE);

            if ($chunk === false) {
                break;
            }

            // Envia chunk para o cliente
            echo $chunk;

            // Força envio imediato para o cliente
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }
    }

    /**
     * Sanitiza nome do arquivo para uso em headers HTTP.
     * Remove caracteres perigosos e escapa aspas.
     */
    private function sanitizeFileName(string $fileName): string
    {
        // Remove caracteres de controle e aspas duplas
        $safe = preg_replace('/[\x00-\x1f\x7f"]/', '', $fileName);

        // Fallback se ficou vazio
        if (empty($safe)) {
            $safe = 'download';
        }

        return $safe;
    }
}
