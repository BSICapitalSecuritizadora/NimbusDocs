<?php

declare(strict_types=1);

namespace App\Infrastructure\Audit;

use App\Domain\Repository\AuditLogRepository;

final class AuditLogger
{
    public function __construct(
        private AuditLogRepository $repo,
        private array $config
    ) {
    }

    public function log(array $data): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // --- ANONIMIZAÇÃO (LGPD) ---
        if (isset($data['details']) && is_array($data['details'])) {
            $data['details'] = $this->redactDetails($data['details']);
        }
        // ---------------------------

        $payload = array_merge([
            'ip_address' => $ip,
            'user_agent' => $ua,
        ], $data);

        $this->repo->create($payload);
    }

    /**
     * Mascara dados sensíveis no array de detalhes
     */
    private function redactDetails(array $context): array
    {
        $sensitiveKeys = [
            'password', 'password_confirmation', 'senha',
            'secret', 'token', 'access_code', 'code', 'auth_token',
            'cpf', 'rg', 'credit_card', 'card_number', 'cvv',
        ];

        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $context[$key] = $this->redactDetails($value);
            } elseif (is_string($key)) {
                // Remove chaves sensíveis
                foreach ($sensitiveKeys as $sensitive) {
                    if (stripos($key, $sensitive) !== false) {
                        $context[$key] = '[REDACTED]';
                        break;
                    }
                }
            }
        }

        return $context;
    }

    public function adminAction(array $data): void
    {
        $data['actor_type'] = 'ADMIN';
        $this->log($data);
    }

    public function portalUserAction(array $data): void
    {
        $data['actor_type'] = 'PORTAL_USER';
        $this->log($data);
    }

    public function systemAction(array $data): void
    {
        $data['actor_type'] = 'SYSTEM';
        $this->log($data);
    }

    /**
     * Helper to log changes with diff.
     */
    public function diff(string $action, $target, ?string $summary, array $oldData, array $newData): void
    {
        $diff = \App\Support\DiffCalculator::compute($oldData, $newData);

        $data = [
            'action' => $action,
            'summary' => $summary,
            'details' => $diff,
        ];

        // Determine target type/id from object if possible, or pass explicitly
        if (is_object($target)) {
            $data['target_type'] = (new \ReflectionClass($target))->getShortName();
            $data['target_id'] = method_exists($target, 'getId') ? $target->getId() : null;
        } elseif (is_array($target) && isset($target['type'], $target['id'])) {
            $data['target_type'] = $target['type'];
            $data['target_id'] = $target['id'];
        }

        $this->log($data);
    }
}
