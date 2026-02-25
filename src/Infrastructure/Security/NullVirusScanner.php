<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Service\VirusScannerInterface;
use Psr\Log\LoggerInterface;

final class NullVirusScanner implements VirusScannerInterface
{
    public function __construct(private ?LoggerInterface $logger = null)
    {
    }

    public function isClean(string $filePath): bool
    {
        // Em dev/local ou sem ClamAV, assumimos que está limpo.
        if ($this->logger) {
            $this->logger->info("[NullVirusScanner] Skipping virus scan for: $filePath");
        }

        return true;
    }

    public function getLastVirusName(): ?string
    {
        return null;
    }
}
