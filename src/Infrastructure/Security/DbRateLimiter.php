<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use PDO;

class DbRateLimiter
{
    public function __construct(private PDO $pdo) {}

    /**
     * Verifica e incrementa o contador. Retorna TRUE se bloqueado.
     * 
     * @param string $scope Contexto (ex: 'login_portal')
     * @param string $ip IP do usuário
     * @param string|null $identifier Identificador opcional (ex: código tentado)
     * @param int $maxAttempts Máximo de tentativas permitidas
     * @param int $decayMinutes Tempo de bloqueio em minutos
     * @return bool True se excedeu o limite (bloqueado), False se permitido
     */
    public function check(string $scope, string $ip, ?string $identifier = null, int $maxAttempts = 5, int $decayMinutes = 10): bool
    {
        $identifier = $identifier ?? '';

        // 1. Limpa registros antigos desse IP/Identifier (opcional, ou pode ser via cron)
        // Aqui vamos manter simples e focar no registro atual.
        
        // 2. Busca registro atual
        $stmt = $this->pdo->prepare(
            "SELECT * FROM auth_rate_limits 
             WHERE scope = :scope AND ip = :ip AND identifier = :ident 
             LIMIT 1"
        );
        $stmt->execute([
            ':scope' => $scope,
            ':ip'    => $ip,
            ':ident' => $identifier
        ]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            // Verifica se está bloqueado
            if ($record['blocked_until'] && new \DateTime($record['blocked_until']) > new \DateTime()) {
                return true;
            }

            // Se o tempo de bloqueio já passou, reseta?
            // Ou se a última tentativa foi há muito tempo (reset window), reseta.
            // Vamos simplificar: se blocked_until passou, permite e reseta se necessário.
            if ($record['blocked_until'] && new \DateTime($record['blocked_until']) <= new \DateTime()) {
                $this->reset($scope, $ip, $identifier);
                $record = null; // Trata como novo
            }
        }

        if (!$record) {
            // Cria registro inicial
            $this->create($scope, $ip, $identifier);
            return false;
        }

        // Se chegou aqui, existe registro e NÃO está bloqueado.
        // Verificamos tries isoladamente?
        // Na verdade o check() normalmente só VERIFICA.
        // Mas para facilitar, vamos fazer o método 'attempt' que verifica E incrementa.
        // O padrão Laravel é: tooManyAttempts (só check) e hit (increment).
        
        if ($record['attempts'] >= $maxAttempts) {
             // Deveria estar bloqueado, mas blocked_until é null ou passado?
             // Se attempts >= max, bloqueia AGORA.
             $this->block($scope, $ip, $identifier, $decayMinutes);
             return true;
        }

        return false;
    }

    /**
     * Incrementa o contador de tentativas.
     */
    public function increment(string $scope, string $ip, ?string $identifier = null, int $maxAttempts = 5, int $decayMinutes = 10): void
    {
        $identifier = $identifier ?? '';
        
        // Garante que existe
        if (!$this->exists($scope, $ip, $identifier)) {
            $this->create($scope, $ip, $identifier);
        }

        $stmt = $this->pdo->prepare(
            "UPDATE auth_rate_limits 
             SET attempts = attempts + 1, 
                 last_attempt_at = NOW() 
             WHERE scope = :scope AND ip = :ip AND identifier = :ident"
        );
        $stmt->execute([
            ':scope' => $scope,
            ':ip'    => $ip,
            ':ident' => $identifier
        ]);

        // Verifica se estourou após incrementar
        $this->checkAndBlockIfNeeded($scope, $ip, $identifier, $maxAttempts, $decayMinutes);
    }

    /**
     * Reseta as tentativas (sucesso no login).
     */
    public function reset(string $scope, string $ip, ?string $identifier = null): void
    {
        $identifier = $identifier ?? '';
        $stmt = $this->pdo->prepare(
            "DELETE FROM auth_rate_limits WHERE scope = :scope AND ip = :ip AND identifier = :ident"
        );
        $stmt->execute([
            ':scope' => $scope,
            ':ip'    => $ip,
            ':ident' => $identifier
        ]);
    }

    private function exists(string $scope, string $ip, string $identifier): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM auth_rate_limits WHERE scope = :scope AND ip = :ip AND identifier = :ident"
        );
        $stmt->execute([':scope' => $scope, ':ip' => $ip, ':ident' => $identifier]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function create(string $scope, string $ip, string $identifier): void
    {
        // Use INSERT IGNORE ou ON DUPLICATE UPDATE para concorrência
        $stmt = $this->pdo->prepare(
            "INSERT INTO auth_rate_limits (scope, ip, identifier, attempts, last_attempt_at)
             VALUES (:scope, :ip, :ident, 0, NOW())
             ON DUPLICATE KEY UPDATE last_attempt_at = NOW()"
        );
        $stmt->execute([':scope' => $scope, ':ip' => $ip, ':ident' => $identifier]);
    }

    private function checkAndBlockIfNeeded(string $scope, string $ip, string $identifier, int $max, int $minutes): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT attempts FROM auth_rate_limits WHERE scope = :scope AND ip = :ip AND identifier = :ident"
        );
        $stmt->execute([':scope' => $scope, ':ip' => $ip, ':ident' => $identifier]);
        $attempts = (int)$stmt->fetchColumn();

        if ($attempts >= $max) {
            $this->block($scope, $ip, $identifier, $minutes);
        }
    }

    private function block(string $scope, string $ip, string $identifier, int $minutes): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE auth_rate_limits 
             SET blocked_until = DATE_ADD(NOW(), INTERVAL :min MINUTE) 
             WHERE scope = :scope AND ip = :ip AND identifier = :ident"
        );
        $stmt->execute([
             ':min' => $minutes,
             ':scope' => $scope, 
             ':ip' => $ip, 
             ':ident' => $identifier
        ]);
    }
}
