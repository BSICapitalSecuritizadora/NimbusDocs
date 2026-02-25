<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Circuit Breaker simples para proteção de chamadas externas.
 *
 * Estados:
 * - CLOSED: Normal, chamadas passam
 * - OPEN: Falhas demais, bloqueia chamadas por X segundos
 * - HALF_OPEN: Teste após timeout, uma chamada passa para verificar
 *
 * Uso:
 *   $cb = new CircuitBreaker('graph-mail', failureThreshold: 5, resetTimeout: 60);
 *   if (!$cb->isAvailable()) {
 *       // Circuit está aberto, não tente
 *   }
 *   try {
 *       $result = callExternalApi();
 *       $cb->recordSuccess();
 *   } catch (\Throwable $e) {
 *       $cb->recordFailure();
 *       throw $e;
 *   }
 */
final class CircuitBreaker
{
    private const STATE_CLOSED = 'closed';
    private const STATE_OPEN = 'open';
    private const STATE_HALF_OPEN = 'half_open';

    private string $name;

    private int $failureThreshold;

    private int $resetTimeout;

    private string $storagePath;

    /**
     * @param string $name Nome único do circuit (ex: 'graph-mail')
     * @param int $failureThreshold Número de falhas consecutivas para abrir o circuit
     * @param int $resetTimeout Segundos para tentar novamente após abrir
     */
    public function __construct(
        string $name,
        int $failureThreshold = 5,
        int $resetTimeout = 60
    ) {
        $this->name = $name;
        $this->failureThreshold = max(1, $failureThreshold);
        $this->resetTimeout = max(10, $resetTimeout);

        // Armazena estado em arquivo temporário
        $this->storagePath = sys_get_temp_dir() . '/circuit_breaker_' . preg_replace('/[^a-z0-9_-]/i', '_', $name) . '.json';
    }

    /**
     * Verifica se o circuit está disponível para chamadas.
     */
    public function isAvailable(): bool
    {
        $state = $this->getState();

        if ($state['status'] === self::STATE_CLOSED) {
            return true;
        }

        if ($state['status'] === self::STATE_OPEN) {
            // Verifica se já passou o timeout para tentar novamente
            if (time() >= $state['open_until']) {
                // Transição para half-open
                $this->setState([
                    'status' => self::STATE_HALF_OPEN,
                    'failures' => $state['failures'],
                    'open_until' => 0,
                    'last_failure' => $state['last_failure'] ?? null,
                ]);

                return true; // Permite uma tentativa
            }

            return false;
        }

        // HALF_OPEN: permite tentativa
        return true;
    }

    /**
     * Registra uma chamada bem-sucedida.
     */
    public function recordSuccess(): void
    {
        $this->setState([
            'status' => self::STATE_CLOSED,
            'failures' => 0,
            'open_until' => 0,
            'last_success' => time(),
        ]);
    }

    /**
     * Registra uma falha.
     */
    public function recordFailure(): void
    {
        $state = $this->getState();
        $failures = ($state['failures'] ?? 0) + 1;

        if ($state['status'] === self::STATE_HALF_OPEN) {
            // Falha durante half-open: volta para open
            $this->setState([
                'status' => self::STATE_OPEN,
                'failures' => $failures,
                'open_until' => time() + $this->resetTimeout,
                'last_failure' => time(),
            ]);

            return;
        }

        if ($failures >= $this->failureThreshold) {
            // Threshold atingido: abre o circuit
            $this->setState([
                'status' => self::STATE_OPEN,
                'failures' => $failures,
                'open_until' => time() + $this->resetTimeout,
                'last_failure' => time(),
            ]);
        } else {
            // Incrementa contador
            $this->setState([
                'status' => self::STATE_CLOSED,
                'failures' => $failures,
                'open_until' => 0,
                'last_failure' => time(),
            ]);
        }
    }

    /**
     * Retorna informações do estado atual.
     */
    public function getInfo(): array
    {
        $state = $this->getState();

        return [
            'name' => $this->name,
            'status' => $state['status'],
            'failures' => $state['failures'],
            'failure_threshold' => $this->failureThreshold,
            'reset_timeout' => $this->resetTimeout,
            'open_until' => $state['open_until'] > 0 ? date('Y-m-d H:i:s', $state['open_until']) : null,
            'last_failure' => isset($state['last_failure']) ? date('Y-m-d H:i:s', $state['last_failure']) : null,
            'last_success' => isset($state['last_success']) ? date('Y-m-d H:i:s', $state['last_success']) : null,
        ];
    }

    /**
     * Força reset do circuit para estado fechado.
     */
    public function reset(): void
    {
        $this->setState([
            'status' => self::STATE_CLOSED,
            'failures' => 0,
            'open_until' => 0,
        ]);
    }

    private function getState(): array
    {
        if (!file_exists($this->storagePath)) {
            return [
                'status' => self::STATE_CLOSED,
                'failures' => 0,
                'open_until' => 0,
            ];
        }

        $content = file_get_contents($this->storagePath);
        $state = json_decode($content ?: '{}', true);

        return [
            'status' => $state['status'] ?? self::STATE_CLOSED,
            'failures' => (int) ($state['failures'] ?? 0),
            'open_until' => (int) ($state['open_until'] ?? 0),
            'last_failure' => $state['last_failure'] ?? null,
            'last_success' => $state['last_success'] ?? null,
        ];
    }

    private function setState(array $state): void
    {
        file_put_contents(
            $this->storagePath,
            json_encode($state, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}
