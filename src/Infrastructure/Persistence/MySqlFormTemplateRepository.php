<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use PDO;

/**
 * Repository para Templates de Formulário Versionados.
 */
final class MySqlFormTemplateRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Retorna template por código.
     */
    public function getByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM form_templates WHERE code = :code AND is_active = 1
        ');
        $stmt->execute([':code' => $code]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Retorna versão atual de um template.
     */
    public function getCurrentVersion(int $templateId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM form_template_versions 
            WHERE template_id = :id AND is_current = 1
            LIMIT 1
        ');
        $stmt->execute([':id' => $templateId]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($version && isset($version['schema_json'])) {
            $version['schema'] = json_decode($version['schema_json'], true);
        }

        return $version ?: null;
    }

    /**
     * Retorna versão específica de um template.
     */
    public function getVersion(int $versionId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT ftv.*, ft.code, ft.name as template_name
            FROM form_template_versions ftv
            JOIN form_templates ft ON ftv.template_id = ft.id
            WHERE ftv.id = :id
        ');
        $stmt->execute([':id' => $versionId]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($version && isset($version['schema_json'])) {
            $version['schema'] = json_decode($version['schema_json'], true);
        }

        return $version ?: null;
    }

    /**
     * Retorna todas as versões de um template.
     */
    public function getVersions(int $templateId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT id, version, is_current, changelog, published_at, created_at
            FROM form_template_versions 
            WHERE template_id = :id
            ORDER BY version DESC
        ');
        $stmt->execute([':id' => $templateId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Cria nova versão de um template.
     */
    public function createVersion(int $templateId, array $schema, ?string $changelog = null, ?int $createdBy = null): int
    {
        // Pega próximo número de versão
        $stmt = $this->pdo->prepare('
            SELECT COALESCE(MAX(version), 0) + 1 as next_version 
            FROM form_template_versions WHERE template_id = :id
        ');
        $stmt->execute([':id' => $templateId]);
        $nextVersion = (int) $stmt->fetchColumn();

        $sql = '
            INSERT INTO form_template_versions 
            (template_id, version, schema_json, changelog, created_by)
            VALUES (:template_id, :version, :schema, :changelog, :created_by)
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':template_id' => $templateId,
            ':version' => $nextVersion,
            ':schema' => json_encode($schema, JSON_UNESCAPED_UNICODE),
            ':changelog' => $changelog,
            ':created_by' => $createdBy,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Publica uma versão (torna ela a versão atual).
     */
    public function publishVersion(int $versionId): bool
    {
        // Pega template_id
        $stmt = $this->pdo->prepare('SELECT template_id FROM form_template_versions WHERE id = :id');
        $stmt->execute([':id' => $versionId]);
        $templateId = (int) $stmt->fetchColumn();

        if (!$templateId) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            // Remove is_current das outras versões
            $this->pdo->prepare('
                UPDATE form_template_versions SET is_current = 0 WHERE template_id = :tid
            ')->execute([':tid' => $templateId]);

            // Marca esta como current
            $this->pdo->prepare('
                UPDATE form_template_versions SET is_current = 1, published_at = NOW() WHERE id = :id
            ')->execute([':id' => $versionId]);

            $this->pdo->commit();

            return true;

        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            return false;
        }
    }

    /**
     * Retorna schema para uma submissão (usa versão vinculada ou atual).
     */
    public function getSchemaForSubmission(int $submissionId): ?array
    {
        // Primeiro tenta versão vinculada à submissão
        $stmt = $this->pdo->prepare('
            SELECT form_template_version_id FROM portal_submissions WHERE id = :id
        ');
        $stmt->execute([':id' => $submissionId]);
        $versionId = $stmt->fetchColumn();

        if ($versionId) {
            return $this->getVersion((int) $versionId);
        }

        // Fallback: versão atual do template padrão
        $template = $this->getByCode('submission_default');
        if ($template) {
            return $this->getCurrentVersion((int) $template['id']);
        }

        return null;
    }

    /**
     * Lista todos os templates ativos.
     */
    public function getAllActive(): array
    {
        return $this->pdo->query('
            SELECT ft.*, 
                   (SELECT MAX(version) FROM form_template_versions WHERE template_id = ft.id) as latest_version
            FROM form_templates ft 
            WHERE ft.is_active = 1
            ORDER BY ft.name
        ')->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
