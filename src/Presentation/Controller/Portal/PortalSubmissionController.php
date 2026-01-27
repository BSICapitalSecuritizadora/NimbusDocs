<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionFileRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionNoteRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionShareholderRepository;
use App\Infrastructure\Integration\CachedCnpjService;
use App\Infrastructure\Integration\CnpjWsService;
use App\Support\Csrf;
use App\Support\AuditLogger;
use App\Support\Session;
use App\Support\RandomToken;
use App\Support\Auth;
use App\Support\FileUpload;
use Respect\Validation\Validator as v;

final class PortalSubmissionController
{
    private MySqlPortalSubmissionRepository $repo;
    private MySqlPortalSubmissionFileRepository $fileRepo;
    private MySqlPortalSubmissionNoteRepository $noteRepo;
    private MySqlPortalSubmissionShareholderRepository $shareholderRepo;
    private AuditLogger $audit;
    private CachedCnpjService $cnpjService;

    public function __construct(private array $config)
    {
        $this->repo            = new MySqlPortalSubmissionRepository($config['pdo']);
        $this->fileRepo        = new MySqlPortalSubmissionFileRepository($config['pdo']);
        $this->noteRepo        = new MySqlPortalSubmissionNoteRepository($config['pdo']);
        $this->shareholderRepo = new MySqlPortalSubmissionShareholderRepository($config['pdo']);
        $this->audit           = new AuditLogger($config['pdo']);
        
        // Usa o serviço de CNPJ com cache do bootstrap
        $this->cnpjService     = $config['cnpj_service'] ?? new CachedCnpjService(
            new CnpjWsService($config['logger']),
            $config['cache'],
            $config['logger']
        );
    }

    public function getCnpjData(array $vars = []): void
    {
        Auth::requirePortalUser();

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Token CSRF inválido']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $cnpj = $_POST['cnpj'] ?? '';
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        if (!CnpjWsService::isValidCnpj($cnpj)) {
            echo json_encode(['error' => 'CNPJ inválido']);
            return;
        }
        
        $data = $this->cnpjService->getCompanyData($cnpj);
        
        if (!$data) {
            echo json_encode(['error' => 'Não foi possível buscar os dados do CNPJ']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    

    public function index(array $vars = []): void
    {
        $user = Auth::requirePortalUser();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $search  = $_GET['q'] ?? null;

        $pagination = $this->repo->paginateByUser((int)$user['id'], $page, $perPage, $search);

        $pageTitle   = 'Minhas submissões';
        $contentView = __DIR__ . '/../../View/portal/submissions/index.php';
        $viewData    = [
            'pagination' => $pagination,
            'flash'      => [
                'success' => Session::getFlash('success'),
                'error'   => Session::getFlash('error'),
            ],
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function showCreateForm(array $vars = []): void
    {
        Auth::requirePortalUser();

        $pageTitle   = 'Nova submissão';
        $contentView = __DIR__ . '/../../View/portal/submissions/create.php';
        $viewData    = [
            'csrfToken' => Csrf::token(),
            'errors'    => Session::getFlash('errors') ?? [],
            'old'       => Session::getFlash('old') ?? [],
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $user  = Auth::requirePortalUser();
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/portal/submissions/create');
        }

        // Coleta todos os dados do formulário
        $data = [
            'title'                => trim($post['title'] ?? 'Cadastro de Cliente'),
            'message'              => trim($post['message'] ?? ''),
            'responsible_name'     => trim($post['responsible_name'] ?? ''),
            'company_cnpj'         => preg_replace('/\D/', '', $post['company_cnpj'] ?? ''),
            'company_name'         => trim($post['company_name'] ?? ''),
            'main_activity'        => trim($post['main_activity'] ?? ''),
            'phone'                => trim($post['phone'] ?? ''),
            'website'              => trim($post['website'] ?? ''),
            'net_worth'            => $this->parseMoney($post['net_worth'] ?? ''),
            'annual_revenue'       => $this->parseMoney($post['annual_revenue'] ?? ''),
            'is_us_person'         => isset($post['is_us_person']) ? 1 : 0,
            'is_pep'               => isset($post['is_pep']) ? 1 : 0,
            'is_none_compliant'    => isset($post['is_none_compliant']) ? 1 : 0,
            'registrant_name'      => trim($post['registrant_name'] ?? ''),
            'registrant_position'  => trim($post['registrant_position'] ?? ''),
            'registrant_rg'        => trim($post['registrant_rg'] ?? ''),
            'registrant_cpf'       => preg_replace('/\D/', '', $post['registrant_cpf'] ?? ''),
        ];

        // Validações
        $errors = [];

        // =========================================================================
        // Validação de Compliance (US Person / PEP / Não se enquadra)
        // CRÍTICO: Esta validação é obrigatória por razões regulatórias
        // =========================================================================
        $hasComplianceDeclaration = $data['is_us_person'] || $data['is_pep'] || $data['is_none_compliant'];
        
        if (!$hasComplianceDeclaration) {
            $errors['compliance'] = 'É obrigatório informar se você é US Person, PEP ou se não se enquadra nessas categorias.';
        }

        if (empty($data['responsible_name'])) {
            $errors['responsible_name'] = 'Nome do responsável é obrigatório.';
        }

        if (empty($data['company_cnpj']) || !CnpjWsService::isValidCnpj($data['company_cnpj'])) {
            $errors['company_cnpj'] = 'CNPJ inválido.';
        }

        if (empty($data['company_name'])) {
            $errors['company_name'] = 'Nome da empresa é obrigatório.';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Telefone é obrigatório.';
        } elseif (!$this->isValidPhone($data['phone'])) {
            $errors['phone'] = 'Formato de telefone inválido. Use (XX) XXXXX-XXXX ou similar.';
        }

        if ($data['net_worth'] === null || $data['net_worth'] <= 0) {
            $errors['net_worth'] = 'Patrimônio líquido inválido.';
        }

        if ($data['annual_revenue'] === null || $data['annual_revenue'] <= 0) {
            $errors['annual_revenue'] = 'Faturamento anual inválido.';
        }

        if (empty($data['registrant_name'])) {
            $errors['registrant_name'] = 'Nome do responsável pelo cadastro é obrigatório.';
        }

        if (empty($data['registrant_cpf']) || !$this->isValidCpf($data['registrant_cpf'])) {
            $errors['registrant_cpf'] = 'CPF do responsável inválido.';
        }

        // Valida composição societária
        $shareholders = json_decode($post['shareholders'] ?? '[]', true);
        
        if (empty($shareholders) || !is_array($shareholders)) {
            $errors['shareholders'] = 'É necessário informar pelo menos um sócio.';
        } else {
            $totalPercentage = 0;
            foreach ($shareholders as $idx => $shareholder) {
                $percentage = (float)($shareholder['percentage'] ?? 0);
                $totalPercentage += $percentage;

                if (empty($shareholder['name'])) {
                    $errors['shareholders'] = 'Nome do sócio é obrigatório.';
                    break;
                }

                if ($percentage <= 0) {
                    $errors['shareholders'] = 'Porcentagem deve ser maior que zero.';
                    break;
                }
            }

            if (abs($totalPercentage - 100) > 0.01) {
                $errors['shareholders'] = sprintf(
                    'A soma das porcentagens deve ser exatamente 100%%. Atual: %.2f%%',
                    $totalPercentage
                );
            }
        }

        // Validação de arquivos obrigatórios
        $requiredFiles = [
            'ultimo_balanco' => 'Último balanço',
            'dre' => 'DRE',
            'politicas' => 'Políticas',
            'cartao_cnpj' => 'Cartão CNPJ',
            'procuracao' => 'Procuração',
            'ata' => 'Ata',
            'contrato_social' => 'Contrato social',
            'estatuto' => 'Estatuto',
        ];

        foreach ($requiredFiles as $field => $label) {
            if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[$field] = "$label é obrigatório.";
            } elseif ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                $errors[$field] = "Erro ao enviar $label.";
            }
        }

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            Session::flash('old_shareholders', $shareholders ?? []);
            $this->redirect('/portal/submissions/create');
        }

        // --- Cria submissão ---
        $refCode = sprintf(
            'SUB-%s-%s',
            date('Ymd'),
            substr(RandomToken::shortCode(8), 0, 8)
        );

        $ip = $_SERVER['REMOTE_ADDR']      ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT']  ?? '';

        $submissionId = $this->repo->createForUser((int)$user['id'], [
            'reference_code'      => $refCode,
            'title'               => $data['title'],
            'message'             => $data['message'],
            'status'              => 'PENDING',
            'created_ip'          => $ip,
            'created_user_agent'  => $ua,
            'responsible_name'    => $data['responsible_name'],
            'company_cnpj'        => $data['company_cnpj'],
            'company_name'        => $data['company_name'],
            'main_activity'       => $data['main_activity'],
            'phone'               => $data['phone'],
            'website'             => $data['website'],
            'net_worth'           => $data['net_worth'],
            'annual_revenue'      => $data['annual_revenue'],
            'is_us_person'        => $data['is_us_person'],
            'is_pep'              => $data['is_pep'],
            'registrant_name'     => $data['registrant_name'],
            'registrant_position' => $data['registrant_position'],
            'registrant_rg'       => $data['registrant_rg'],
            'registrant_cpf'      => $data['registrant_cpf'],
        ]);

        // Salva composição societária
        foreach ($shareholders as $shareholder) {
            $this->shareholderRepo->create($submissionId, [
                'name'          => $shareholder['name'],
                'document_rg'   => $shareholder['rg'] ?? null,
                'document_cnpj' => preg_replace('/\D/', '', $shareholder['cnpj'] ?? ''),
                'percentage'    => (float)$shareholder['percentage'],
            ]);
        }

        // Salva arquivos obrigatórios com tipo específico
        $userId = (int)$user['id'];
        $storageBase = dirname(__DIR__, 4) . '/storage/portal_uploads/' . $userId . '/';

        $fileTypeMap = [
            'ultimo_balanco'            => 'BALANCE_SHEET',
            'dre'                       => 'DRE',
            'politicas'                 => 'POLICIES',
            'cartao_cnpj'               => 'CNPJ_CARD',
            'procuracao'                => 'POWER_OF_ATTORNEY',
            'ata'                       => 'MINUTES',
            'contrato_social'           => 'ARTICLES_OF_INCORPORATION',
            'estatuto'                  => 'BYLAWS',
        ];

        foreach ($fileTypeMap as $field => $docType) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                try {
                    $stored = FileUpload::store($_FILES[$field], $storageBase);
                    
                    $storedName   = basename($stored['path']);
                    $checksum     = is_file($stored['path']) ? hash_file('sha256', $stored['path']) : null;
                    $relativePath = 'portal_uploads/' . $userId . '/' . $storedName;

                    $this->fileRepo->create($submissionId, [
                        'origin'          => 'USER',
                        'original_name'   => $stored['original_name'],
                        'stored_name'     => $storedName,
                        'mime_type'       => $stored['mime_type'],
                        'size_bytes'      => (int)$stored['size'],
                        'storage_path'    => $relativePath,
                        'checksum'        => $checksum,
                        'visible_to_user' => 0,
                        'document_type'   => $docType,
                    ]);
                } catch (\Throwable $e) {
                    $this->audit->log('PORTAL_USER', (int)$user['id'], 'USER_FILE_UPLOAD_FAILED', 'PORTAL_SUBMISSION', $submissionId, [
                        'error' => $e->getMessage(),
                        'file'  => $_FILES[$field]['name'] ?? null,
                        'type'  => $docType,
                    ]);
                }
            }
        }

        $this->audit->log('PORTAL_USER', (int)$user['id'], 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', $submissionId, [
            'reference_code' => $refCode,
        ]);

        $this->config['audit']->portalUserAction([
            'actor_id'     => (int)$user['id'],
            'actor_name'   => $user['full_name'] ?? $user['name'] ?? $user['email'],
            'action'       => 'PORTAL_SUBMISSION_CREATED',
            'summary'      => 'Nova submissão de cadastro criada.',
            'context_type' => 'submission',
            'context_id'   => $submissionId,
            'details'      => [
                'company_name' => $data['company_name'],
                'cnpj'         => CnpjWsService::formatCnpj($data['company_cnpj']),
            ],
        ]);

        // Notificações
        try {
            $submission = $this->repo->findById($submissionId);
            $portalUser = $this->config['portal_user_repo']->findById((int)$user['id']);
            
            if ($submission && $portalUser && $this->config['notification']) {
                $this->config['notification']->notifySubmissionReceived($submission, $portalUser);
            }
        } catch (\Exception $e) {
            // Não impede a criação da submissão se notificação falhar
            error_log('Erro ao notificar sobre nova submissão: ' . $e->getMessage());
        }

        Session::flash('success', 'Cadastro enviado com sucesso.');
        $this->redirect('/portal/submissions/' . $submissionId);
    }

    public function show(array $vars = []): void
    {
        $user = Auth::requirePortalUser();
        $userId = (int)$user['id'];
        $id = (int)($vars['id'] ?? 0);

        $submission = $this->repo->findForUser($id, $userId);
        if (!$submission) {
            http_response_code(404);
            echo 'Envio não encontrado.';
            return;
        }

        // Log de acesso
        $logger = $this->config['portal_access_logger'] ?? null;
        if ($logger) {
            $logger->log((int)$userId, 'VIEW_SUBMISSION', 'submission', $id);
        }

        $files = $this->fileRepo->findBySubmission($id);
        $notes = $this->noteRepo->listVisibleForSubmission($id);
        $shareholders = $this->shareholderRepo->findBySubmission($id);
        $responseFiles = $this->fileRepo->findVisibleToUser($id);

        $pageTitle   = 'Detalhes da submissão';
        $contentView = __DIR__ . '/../../View/portal/submissions/show.php';
        $viewData = [
            'submission'    => $submission,
            'files'         => $files,
            'responseFiles' => $responseFiles,
            'notes'         => $notes,
            'shareholders'  => $shareholders,
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /**
     * Converte string de dinheiro (R$ 1.000.000,00) para float
     */
    private function parseMoney(string $value): ?float
    {
        if (empty($value)) {
            return null;
        }

        // Remove tudo exceto números, vírgula e ponto
        $value = preg_replace('/[^\d,.]/', '', $value);
        
        // Substitui vírgula por ponto
        $value = str_replace(',', '.', $value);
        
        // Remove pontos extras (mantém apenas o último como decimal)
        $parts = explode('.', $value);
        if (count($parts) > 2) {
            $decimal = array_pop($parts);
            $value = implode('', $parts) . '.' . $decimal;
        }

        return (float)$value;
    }

    /**
     * Valida CPF
     */
    private function isValidCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/^(\d)\1*$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida formato de telefone
     * Aceita: (11) 99999-9999, (11) 3333-3333, 11999999999
     */
    private function isValidPhone(string $phone): bool
    {
        $phone = preg_replace('/\D/', '', $phone);
        // Verifica se tem 10 ou 11 dígitos (DDD + número)
        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }
}
