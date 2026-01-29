<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Service\VirusScannerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Integração com ClamAV via Socket (TCP ou UNIX)
 * Protocolo INSTREAM para enviar streams sem salvar em tmp temporário do AV.
 */
final class ClamAvScanner implements VirusScannerInterface
{
    private string $host;
    private int $port;
    private int $timeout;
    private ?string $lastVirus = null;

    public function __construct(
        string $host = '127.0.0.1',
        int $port = 3310,
        int $timeout = 30,
        private ?LoggerInterface $logger = null
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function isClean(string $filePath): bool
    {
        $this->lastVirus = null;

        if (!file_exists($filePath)) {
            throw new RuntimeException("Arquivo não encontrado para scan: $filePath");
        }

        $socket = @fsockopen($this->host, $this->port, $errorCode, $errorMessage, $this->timeout);

        if (!$socket) {
            // Se não conseguir conectar, por padrão pode ser FAIL-OPEN (log warning e deixa passar)
            // ou FAIL-CLOSED (lança exception).
            // Aqui vamos implementar FAIL-OPEN com log severo, para não parar o negócio se o AV cair.
            // Ajuste conforme política de segurança.
            if ($this->logger) {
                $this->logger->warning("[ClamAvScanner] Connection failed to {$this->host}:{$this->port}. Error: $errorMessage");
            }
            return true; // Fail-open (permite upload se AV estiver fora)
        }

        stream_set_timeout($socket, $this->timeout);

        // Protocolo ClamAV: zINSTREAM\0
        // Envia o arquivo em chunks
        fwrite($socket, "zINSTREAM\0");

        $handle = fopen($filePath, 'rb');
        while (!feof($handle)) {
            $chunk = fread($handle, 8192);
            if ($chunk === false || strlen($chunk) === 0) {
                break;
            }
            // Tamanho do chunk (4 bytes, big endian)
            $size = pack('N', strlen($chunk));
            fwrite($socket, $size . $chunk);
        }
        fclose($handle);

        // Fim do stream: tamanho 0
        fwrite($socket, pack('N', 0));

        // Ler resposta
        $response = trim(fgets($socket) ?: '');
        fclose($socket);

        if ($this->logger) {
            $this->logger->debug("[ClamAvScanner] Scan result for $filePath: $response");
        }

        // Respostas típicas:
        // stream: OK
        // stream: Eicar-Test-Signature FOUND
        // stream: <VirusName> FOUND

        if (str_ends_with($response, 'OK')) {
            return true;
        }

        if (str_contains($response, 'FOUND')) {
            $parts = explode(':', $response);
            // Formato "stream: VirusName FOUND"
            $virusPart = trim($parts[1] ?? 'Unknown');
            $this->lastVirus = preg_replace('/ FOUND$/', '', $virusPart);
            return false;
        }

        // Resposta inesperada
        if ($this->logger) {
            $this->logger->error("[ClamAvScanner] Unexpected response: $response");
        }
        return true; 
    }

    public function getLastVirusName(): ?string
    {
        return $this->lastVirus;
    }
}
