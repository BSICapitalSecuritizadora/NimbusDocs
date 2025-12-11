<?php

declare(strict_types=1);

namespace App\Aplication;

use App\Infrastructure\Persistence\MySqlGeneralDocumentRepository;
use App\Infrastructure\Persistence\MySqlDocumentCategoryRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Application\Service\NotificationService;
use App\Support\FileUpload;
use App\Support\AuditLogger;
use Respect\Validation\Validator as v;

final class GeneralDocumentService
{
    private MySqlGeneralDocumentRepository $documentRepo;
    private MySqlDocumentCategoryRepository $categoryRepo;
    private MySqlPortalUserRepository $userRepo;
    private NotificationService $notificationService;
    private AuditLogger $audit;
    private FileUpload $fileUpload;

    public function __construct(
        private array $config,
        ?NotificationService $notificationService = null
    ) {
        $this->documentRepo = new MySqlGeneralDocumentRepository($config['pdo']);
        $this->categoryRepo = new MySqlDocumentCategoryRepository($config['pdo']);
        $this->userRepo     = new MySqlPortalUserRepository($config['pdo']);
        $this->audit        = new AuditLogger($config['pdo']);
        $this->fileUpload   = new FileUpload();
        
        // Permite injeção ou cria nova instância
        if ($notificationService === null) {
            $graphMailConfig = [
                'GRAPH_TENANT_ID'      => $config['graph_tenant_id'] ?? '',
                'GRAPH_CLIENT_ID'      => $config['graph_client_id'] ?? '',
                'GRAPH_CLIENT_SECRET'  => $config['graph_client_secret'] ?? '',
                'MAIL_FROM'            => $config['graph_sender_email'] ?? '',
                'MAIL_FROM_NAME'       => 'NimbusDocs'
            ];
            
            $logger = $config['logger'] ?? new \Monolog\Logger('app');
            $graphMailService = new \App\Infrastructure\Notification\GraphMailService($graphMailConfig, $logger);
            $this->notificationService = new NotificationService($graphMailService);
        } else {
            $this->notificationService = $notificationService;
        }
    }

    /**
     * Cria um novo documento geral com validação e upload de arquivo.
     * @param array<string,mixed> $data
     * @return array{success:bool,id?:int,errors?:array<string,string>}
     */
    public function createDocument(array $data, int $adminId): array
    {
        // Validação de dados
        $errors = $this->validateDocumentData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Valida se categoria existe
        if (!$this->categoryRepo->find((int)$data['category_id'])) {
            return [
                'success' => false,
                'errors' => ['category_id' => 'Categoria não encontrada.'],
            ];
        }

        // Processa upload de arquivo
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'errors' => ['file' => 'Arquivo inválido ou não foi enviado.'],
            ];
        }

        try {
            $uploadResult = $this->fileUpload::store(
                $_FILES['file'],
                'storage/documents'
            );

            if (!$uploadResult['success']) {
                return [
                    'success' => false,
                    'errors' => ['file' => $uploadResult['message']],
                ];
            }

            // Prepara dados para inserção
            $documentData = [
                'category_id'        => (int)$data['category_id'],
                'title'              => trim($data['title']),
                'description'        => trim($data['description'] ?? ''),
                'file_path'          => $uploadResult['path'],
                'file_mime'          => $uploadResult['mime_type'],
                'file_size'          => $uploadResult['size'],
                'file_original_name' => $uploadResult['original_name'],
                'is_active'          => (int)($data['is_active'] ?? 1),
                'published_at'       => $data['published_at'] ?? null,
                'created_by_admin'   => $adminId,
            ];

            $documentId = $this->documentRepo->create($documentData);

            // Registra no audit
            $this->audit->log(
                'ADMIN',
                $adminId,
                'DOCUMENT_CREATED',
                'GENERAL_DOCUMENT',
                $documentId,
                context: [
                    'title'       => $documentData['title'],
                    'category_id' => $documentData['category_id'],
                ]
            );

            // Notifica usuários ativos do portal (se habilitado)
            $notificationsEnabled = ($this->config['settings']['notifications.general_documents.enabled'] ?? '1') === '1';
            
            if ($notificationsEnabled) {
                try {
                    $portalUsers = $this->userRepo->getActiveUsers();
                    $category = $this->categoryRepo->find((int)$data['category_id']);
                    
                    $docWithCategory = array_merge(
                        $this->documentRepo->find($documentId) ?? [],
                        ['category_name' => $category['name'] ?? 'Sem categoria']
                    );
                    
                    $this->notificationService->notifyGeneralDocument($docWithCategory, $portalUsers);
                } catch (\Exception $e) {
                    // Falha na notificação não deve impedir criação do documento
                    error_log('Erro ao enviar notificações de documento: ' . $e->getMessage());
                }
            }

            return ['success' => true, 'id' => $documentId];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['file' => 'Erro ao processar arquivo: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Atualiza um documento existente.
     * @param int $documentId
     * @param array<string,mixed> $data
     * @param int $adminId
     * @return array{success:bool,errors?:array<string,string>}
     */
    public function updateDocument(int $documentId, array $data, int $adminId): array
    {
        // Valida documento existe
        $document = $this->documentRepo->find($documentId);
        if (!$document) {
            return [
                'success' => false,
                'errors' => ['general' => 'Documento não encontrado.'],
            ];
        }

        // Validação básica
        $errors = [];
        if (empty($data['title'])) {
            $errors['title'] = 'Título é obrigatório.';
        }
        if (!empty($data['category_id']) && !$this->categoryRepo->find((int)$data['category_id'])) {
            $errors['category_id'] = 'Categoria não encontrada.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Prepara dados
        $updateData = [
            'category_id' => (int)($data['category_id'] ?? $document['category_id']),
            'title'       => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'is_active'   => (int)($data['is_active'] ?? $document['is_active']),
        ];

        $this->documentRepo->update($documentId, $updateData);

        // Registra no audit
        $this->audit->log(
            'ADMIN',
            $adminId,
            'DOCUMENT_UPDATED',
            'GENERAL_DOCUMENT',
            $documentId,
            context: [
                'title'       => $updateData['title'],
                'is_active'   => $updateData['is_active'],
            ]
        );

        return ['success' => true];
    }

    /**
     * Ativa ou desativa um documento.
     * @param int $documentId
     * @param bool $isActive
     * @param int $adminId
     * @return array{success:bool,message?:string}
     */
    public function toggleActive(int $documentId, bool $isActive, int $adminId): array
    {
        $document = $this->documentRepo->find($documentId);
        if (!$document) {
            return ['success' => false, 'message' => 'Documento não encontrado.'];
        }

        $this->documentRepo->update($documentId, ['is_active' => $isActive ? 1 : 0]);

        $this->audit->log(
            'ADMIN',
            $adminId,
            'DOCUMENT_' . ($isActive ? 'ACTIVATED' : 'DEACTIVATED'),
            'GENERAL_DOCUMENT',
            $documentId
        );

        return ['success' => true];
    }

    /**
     * Deleta um documento.
     * @param int $documentId
     * @param int $adminId
     * @return array{success:bool,message?:string}
     */
    public function deleteDocument(int $documentId, int $adminId): array
    {
        $document = $this->documentRepo->find($documentId);
        if (!$document) {
            return ['success' => false, 'message' => 'Documento não encontrado.'];
        }

        try {
            // Remove arquivo físico se existir
            if (!empty($document['file_path'])) {
                $filePath = dirname(__DIR__, 3) . '/storage/' . ltrim($document['file_path'], '/');
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            $this->documentRepo->delete($documentId);

            // Registra no audit
            $this->audit->log(
                'ADMIN',
                $adminId,
                'DOCUMENT_DELETED',
                'GENERAL_DOCUMENT',
                $documentId,
                context: ['title' => $document['title']]
            );

            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao deletar documento: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Recupera um documento para o portal (ativo apenas).
     * @param int $documentId
     * @return array<string,mixed>|null
     */
    public function getForPortal(int $documentId): ?array
    {
        $document = $this->documentRepo->find($documentId);

        if (!$document || !$document['is_active']) {
            return null;
        }

        return $document;
    }

    /**
     * Lista documentos do portal filtrados por categoria e termo de busca.
     * @param int|null $categoryId
     * @param string|null $searchTerm
     * @return array<int,array<string,mixed>>
     */
    public function listForPortal(?int $categoryId = null, ?string $searchTerm = null): array
    {
        return $this->documentRepo->listForPortal($categoryId, $searchTerm);
    }

    /**
     * Lista todos os documentos (admin view).
     * @return array<int,array<string,mixed>>
     */
    public function listForAdmin(): array
    {
        return $this->documentRepo->listForAdmin();
    }

    /**
     * Recupera detalhes de um documento.
     * @param int $documentId
     * @return array<string,mixed>|null
     */
    public function getDetails(int $documentId): ?array
    {
        return $this->documentRepo->find($documentId);
    }

    /**
     * Lista categorias disponíveis.
     * @return array<int,array<string,mixed>>
     */
    public function getCategories(): array
    {
        return $this->categoryRepo->all();
    }

    /**
     * Valida dados de documento.
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    private function validateDocumentData(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Título é obrigatório.';
        } elseif (!v::stringType()->length(3, 255)->validate($data['title'])) {
            $errors['title'] = 'Título deve ter entre 3 e 255 caracteres.';
        }

        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Categoria é obrigatória.';
        } elseif (!v::intVal()->positive()->validate($data['category_id'])) {
            $errors['category_id'] = 'Categoria inválida.';
        }

        if (isset($data['description']) && !v::stringType()->length(0, 1000)->validate($data['description'])) {
            $errors['description'] = 'Descrição não pode exceder 1000 caracteres.';
        }

        if (isset($data['published_at']) && !empty($data['published_at'])) {
            if (!$this->isValidDate($data['published_at'])) {
                $errors['published_at'] = 'Data de publicação inválida.';
            }
        }

        return $errors;
    }

    /**
     * Valida formato de data (YYYY-MM-DD).
     */
    private function isValidDate(string $date): bool
    {
        $timestamp = strtotime($date);
        return $timestamp !== false;
    }
}
