<?php

declare(strict_types=1);

namespace App\Domain\Service;

interface VirusScannerInterface
{
    /**
     * Verifica se o arquivo está limpo.
     * Retorna true se estiver limpo, false se infectado.
     * Pode lançar exceção em caso de erro de conexão (configuração).
     */
    public function isClean(string $filePath): bool;

    /**
     * Retorna detalhes da última infecção encontrada, se houver.
     */
    public function getLastVirusName(): ?string;
}
