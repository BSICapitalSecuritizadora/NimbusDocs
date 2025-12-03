<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

final class MySqlPortalSubmissionShareholderRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Cria um sócio para uma submissão
     */
    public function create(int $submissionId, array $data): int
    {
        $sql = "INSERT INTO portal_submission_shareholders
                (submission_id, name, document_rg, document_cnpj, percentage)
                VALUES (:submission_id, :name, :document_rg, :document_cnpj, :percentage)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':submission_id' => $submissionId,
            ':name'          => $data['name'],
            ':document_rg'   => $data['document_rg'] ?? null,
            ':document_cnpj' => $data['document_cnpj'] ?? null,
            ':percentage'    => $data['percentage'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Lista todos os sócios de uma submissão
     * @return array<int,array>
     */
    public function findBySubmission(int $submissionId): array
    {
        $sql = "SELECT * FROM portal_submission_shareholders
                WHERE submission_id = :submission_id
                ORDER BY percentage DESC, name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':submission_id' => $submissionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Calcula a soma total de porcentagens para validação
     */
    public function getTotalPercentage(int $submissionId): float
    {
        $sql = "SELECT COALESCE(SUM(percentage), 0) AS total
                FROM portal_submission_shareholders
                WHERE submission_id = :submission_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':submission_id' => $submissionId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    /**
     * Remove todos os sócios de uma submissão (para recriar)
     */
    public function deleteBySubmission(int $submissionId): void
    {
        $sql = "DELETE FROM portal_submission_shareholders WHERE submission_id = :submission_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':submission_id' => $submissionId]);
    }

    /**
     * Remove um sócio específico
     */
    public function delete(int $id): void
    {
        $sql = "DELETE FROM portal_submission_shareholders WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}
