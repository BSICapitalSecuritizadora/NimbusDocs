<?php
/** @var array $pagination */
/** @var string $csrfToken */
$items = $pagination['items'] ?? [];

$actionMap = [
    'SUBMISSION_STATUS_CHANGED' => 'Alteração de status',
    'SUBMISSION_RESPONSE_FILES_UPLOADED' => 'Arquivos de resposta da submissão enviados',
    'USER_UPLOADED_CORRECTION_FILE' => 'Arquivo de correção enviado',
    'LOGIN_SUCCESS' => 'Autenticação concluída com êxito',
    'PORTAL_LOGIN_SUCCESS_CODE' => 'Autenticação via código de acesso ao portal concluída',
    'USER_SUBMITTED_CORRECTIONS' => 'Correções enviadas',
    'PORTAL_USER_UPDATED' => 'Dados do usuário do portal atualizados',
    'FILE_DOWNLOAD' => 'Download de arquivo',
    'PORTAL_LOGIN_CODE_FAILED' => 'Falha na validação do código de acesso ao portal',
    'LOGIN_FAILED' => 'Falha no processo de autenticação',
    'PORTAL_ACCESS_LINK_GENERATED' => 'Link de acesso ao portal gerado',
    'SUBMISSION_NOTIFICATION_RESENT' => 'Notificação reenviada',
    'LOGOUT' => 'Saída do sistema',
    'SUBMISSION_CREATED' => 'Submissão criada',
    'PORTAL_SUBMISSION_CREATED' => 'Submissão criada no portal',
    'PORTAL_USER_CREATED' => 'Usuário do portal criado',
    'FILE_PREVIEW' => 'Visualização de arquivo'
];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-lock-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Trilha de Auditoria</h1>
            <p class="text-muted mb-0 small">Histórico detalhado de segurança e operações do sistema</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-search" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros de Pesquisa</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3" method="get" action="/admin/audit-logs">
            <div class="col-md-3">
                <label class="nd-label">Tipo de Usuário</label>
                <div class="nd-input-group">
                    <select name="actor_type" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todos</option>
                        <option value="ADMIN" <?= ($_GET['actor_type'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
                        <option value="PORTAL_USER" <?= ($_GET['actor_type'] ?? '') === 'PORTAL_USER' ? 'selected' : '' ?>>Usuário Portal</option>
                        <option value="SYSTEM" <?= ($_GET['actor_type'] ?? '') === 'SYSTEM' ? 'selected' : '' ?>>Sistema</option>
                    </select>
                    <i class="bi bi-person-badge nd-input-icon"></i>
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="nd-label">Atividade</label>
                <div class="nd-input-group">
                    <select name="action" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todas as Atividades</option>
                        <?php foreach ($actionMap as $key => $label): ?>
                            <option value="<?= htmlspecialchars($key) ?>" <?= ($_GET['action'] ?? '') === $key ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="bi bi-lightning nd-input-icon"></i>
                </div>
            </div>

            <div class="col-md-4">
                <label class="nd-label">Busca Geral</label>
                <div class="nd-input-group">
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="nd-input"
                        placeholder="Resumo, IP, Nome..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="nd-btn nd-btn-primary w-100" type="submit">
                    <i class="bi bi-filter me-1"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<div class="nd-card">
    <div class="nd-card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
             <i class="bi bi-list-check" style="color: var(--nd-navy-500);"></i>
             <h5 class="nd-card-title mb-0">Registros de Atividade</h5>
        </div>
        <span class="badge bg-light text-dark border">Página <?= (int)$pagination['page'] ?> de <?= (int)($pagination['pages'] ?? 1) ?></span>
    </div>

    <div class="nd-card-body p-0">
         <?php if (!$items): ?>
             <div class="text-center py-5">
                <i class="bi bi-clipboard-x text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum registro de auditoria encontrado para os critérios selecionados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                         <tr>
                            <th style="width: 180px;">Data da Ocorrência</th>
                            <th>Ação Registrada</th>
                            <th>Responsável</th>
                            <th>Recurso / Objeto</th>
                            <th>Detalhes Técnicos</th>
                            <th class="text-end">Endereço IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $log): ?>
                            <tr>
                                <td>
                                     <div class="text-muted small">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($log['occurred_at'] ?? $log['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $action = $log['action'] ?? 'UNKNOWN';
                                        
                                        $normalize = fn($s) => ucwords(strtolower(str_replace(['_', '-'], ' ', $s)));
                                        
                                        // Mapa completo de ações do sistema
                                        $display = match(true) {
                                            // Auth & Security
                                            $action === 'LOGIN_SUCCESS'              => ['Autenticação Bem-sucedida', 'nd-badge-success',   'bi-shield-check'],
                                            $action === 'LOGIN_FAILED'               => ['Falha de Autenticação',     'nd-badge-danger',    'bi-shield-x'],
                                            $action === 'LOGOUT'                     => ['Logout do Sistema',         'nd-badge-secondary', 'bi-box-arrow-left'],
                                            
                                            // Portal Users
                                            // Portal Users
                                            $action === 'PORTAL_USER_CREATED'        => ['Usuário Portal Criado',     'bg-success text-white border-0',   'bi-person-plus-fill'],
                                            $action === 'PORTAL_USER_UPDATED'        => ['Dados de Usuário Atualizados', 'nd-badge-info',   'bi-person-lines-fill'],
                                            $action === 'PORTAL_ACCESS_LINK_GENERATED' => ['Link de Acesso Gerado',    'bg-secondary text-white', 'bi-link-45deg'],
                                            $action === 'PORTAL_LOGIN_SUCCESS_CODE'  => ['Acesso via Token (Portal)', 'nd-badge-success',   'bi-key-fill'],
                                            $action === 'PORTAL_LOGIN_CODE_FAILED'   => ['Falha de Token (Portal)',   'nd-badge-danger',    'bi-key'],

                                            // Submissions
                                            $action === 'SUBMISSION_CREATED'         => ['Nova Submissão',            'nd-badge-success',   'bi-file-earmark-plus'],
                                            $action === 'PORTAL_SUBMISSION_CREATED'  => ['Submissão Enviada (Portal)', 'nd-badge-success',  'bi-file-earmark-arrow-up'],
                                            $action === 'SUBMISSION_STATUS_CHANGED'  => ['Alteração de Status', 'bg-primary text-white border-0', 'bi-arrow-repeat'],
                                            $action === 'SUBMISSION_RESPONSE_FILES_UPLOADED' => ['Resposta Anexada',         'nd-badge-warning',   'bi-paperclip'],
                                            $action === 'USER_SUBMITTED_CORRECTIONS' => ['Correções Enviadas', 'bg-primary text-white border-0', 'bi-check-circle'],
                                            $action === 'USER_UPLOADED_CORRECTION_FILE' => ['Arquivo de Correção Env.', 'bg-info text-dark border-0', 'bi-file-earmark-upload'],
                                            $action === 'SUBMISSION_NOTIFICATION_RESENT' => ['Notificação Reenviada', 'bg-warning text-dark border-0', 'bi-envelope-paper'],
                                            
                                            // Documents & Files
                                            $action === 'FILE_PREVIEW' || str_contains($action, 'Visualização') => ['Visualização de Arquivo',   'bg-secondary text-white border-0',   'bi-eye-fill'],
                                            $action === 'FILE_DOWNLOAD'              => ['Download de Arquivo',       'bg-info text-white border-0', 'bi-download'],
                                            
                                            // Default Fallback with Text Translation
                                            default => (function() use ($action, $normalize) {
                                                // Tenta traduzir termos comuns se não houver match exato
                                                $translated = $action;
                                                $replacements = [
                                                    'CREATE' => 'Criação', 'CREATED' => 'Criado',
                                                    'UPDATE' => 'Edição',  'UPDATED' => 'Atualizado',
                                                    'DELETE' => 'Exclusão','DELETED' => 'Excluído',
                                                    'VIEW'   => 'Visualização',
                                                    'UPLOAD' => 'Envio',
                                                    'SUBMISSION' => 'Submissão',
                                                    'STATUS' => 'Status',
                                                    'CHANGED' => 'Alterado',
                                                    'FILE' => 'Arquivo',
                                                    'USER' => 'Usuário',
                                                    'ERROR' => 'Erro',
                                                    'FAIL' => 'Falha',
                                                    'SUCCESS' => 'Sucesso'
                                                ];
                                                
                                                // Primeiro substitui underscores por espaços
                                                $human = str_replace(['_', '-'], ' ', $translated);
                                                
                                                // Traduz palavras chave
                                                foreach ($replacements as $en => $pt) {
                                                    $human = preg_replace("/\b$en\b/i", $pt, $human);
                                                }
                                                
                                                $label = ucwords(strtolower($human));
                                                
                                                // Define cor baseada no tipo de ação
                                                $class = 'nd-badge-secondary';
                                                $icon = 'bi-activity';
                                                
                                                if (str_contains($action, 'CREATE') || str_contains($action, 'SUCCESS')) {
                                                    $class = 'nd-badge-success';
                                                    $icon = 'bi-check-lg';
                                                } elseif (str_contains($action, 'UPDATE') || str_contains($action, 'CHANGE')) {
                                                    $class = 'nd-badge-warning';
                                                    $icon = 'bi-pencil';
                                                } elseif (str_contains($action, 'DELETE') || str_contains($action, 'FAIL') || str_contains($action, 'ERROR')) {
                                                    $class = 'nd-badge-danger';
                                                    $icon = 'bi-x-lg';
                                                } elseif (str_contains($action, 'VIEW') || str_contains($action, 'PREVIEW')) {
                                                    $class = 'nd-badge-primary';
                                                    $icon = 'bi-eye';
                                                }
                                                
                                                return [$label, $class, $icon];
                                            })()
                                        };
                                    ?>
                                    <span class="nd-badge <?= $display[1] ?>">
                                        <i class="bi <?= $display[2] ?> me-1"></i> <?= htmlspecialchars($display[0], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start small">
                                        <?php
                                            $actorType = $log['actor_type'] ?? 'SYSTEM';
                                            $actorLabel = $actorType;
                                            $actorClass = 'bg-secondary text-white'; // Default

                                            switch ($actorType) {
                                                case 'ADMIN':
                                                    $actorLabel = 'Administrador';
                                                    $actorClass = 'bg-primary text-white shadow-sm'; 
                                                    break;
                                                case 'PORTAL_USER':
                                                    $actorLabel = 'Usuário do Portal';
                                                    $actorClass = 'bg-info text-dark bg-opacity-25 text-info border border-info border-opacity-25'; 
                                                    break;
                                                case 'SYSTEM':
                                                    $actorLabel = 'Sistema';
                                                    $actorClass = 'bg-dark text-white shadow-sm'; 
                                                    break;
                                            }
                                        ?>
                                        <span class="badge rounded-pill fw-normal mb-1 <?= $actorClass ?>">
                                            <?= htmlspecialchars($actorLabel, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <?php if (!empty($log['actor_name'])): ?>
                                             <span class="text-dark"><?= htmlspecialchars($log['actor_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                             <?php if (!empty($log['actor_id'])): ?>
                                                <span class="text-muted" style="font-size: 0.8em;">ID: #<?= (int)$log['actor_id'] ?></span>
                                             <?php endif; ?>
                                        <?php elseif (!empty($log['actor_id'])): ?>
                                             <span class="text-muted">ID: #<?= (int)$log['actor_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start small">
                                         <?php
                                            $targetType = $log['target_type'] ?? '-';
                                            $targetLabel = $targetType;
                                            $targetStyle = '';
                                            $targetClass = 'bg-light text-dark border'; // Default

                                            switch (mb_strtolower($targetType, 'UTF-8')) {
                                                case 'admin_user':
                                                case 'administrador':
                                                    $targetLabel = 'Administrador';
                                                    $targetClass = 'bg-primary text-white border-0';
                                                    break;
                                                case 'portal_user':
                                                case 'usuário do portal':
                                                case 'usuário':
                                                    $targetLabel = 'Usuário do Portal';
                                                    $targetClass = 'bg-info text-dark border-0';
                                                    break;
                                                case 'portal_access_token':
                                                case 'token de acesso':
                                                    $targetLabel = 'Token de Acesso';
                                                    $targetClass = 'bg-dark text-white border-0';
                                                    break;
                                                case 'portal_submission':
                                                case 'submission':
                                                case 'submissão':
                                                    $targetLabel = 'Submissão';
                                                    $targetClass = 'text-white border-0';
                                                    $targetStyle = 'background-color: #6610f2;'; // Indigo
                                                    break;
                                                case 'portal_submission_file':
                                                case 'arquivo de submissão':
                                                    $targetLabel = 'Arquivo de Submissão';
                                                    $targetClass = 'bg-secondary text-white border-0';
                                                    break;
                                            }
                                         ?>
                                         <?php if ($targetType !== '-'): ?>
                                            <span class="badge rounded-pill fw-normal mb-1 <?= $targetClass ?>" style="<?= $targetStyle ?>">
                                                <?= htmlspecialchars($targetLabel, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <?php if (!empty($log['target_id'])): ?>
                                             <span class="text-muted">#<?= (int)$log['target_id'] ?></span>
                                            <?php endif; ?>
                                         <?php else: ?>
                                            <span class="text-muted">-</span>
                                         <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($log['details'])): ?>
                                        <code class="d-block text-truncate small text-muted" style="max-width: 250px;" title="<?= htmlspecialchars($log['details'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($log['details'], ENT_QUOTES, 'UTF-8') ?>
                                        </code>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                     <code class="text-muted small bg-light px-1 rounded border">
                                        <?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </code>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (($pagination['pages'] ?? 1) > 1): ?>
                <div class="d-flex justify-content-center border-top p-3 bg-light rounded-bottom">
                    <nav aria-label="Navegação da auditoria">
                        <ul class="pagination pagination-sm mb-0 shadow-sm">
                            <?php 
                            $current = (int)$pagination['page'];
                            $pages = (int)$pagination['pages'];
                            $range = 2; // Show 2 pages before and after current
                            $start = max(1, $current - $range);
                            $end = min($pages, $current + $range);
                            
                            // Helper to build URL preserving filters
                            $buildUrl = function($p) {
                                $params = $_GET;
                                $params['page'] = $p;
                                return '/admin/audit-logs?' . http_build_query($params);
                            };
                            ?>

                            <!-- First / Previous -->
                            <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link border-0 text-secondary" href="<?= $buildUrl(1) ?>" aria-label="Primeira">
                                    <i class="bi bi-chevron-double-left"></i>
                                </a>
                            </li>
                            <li class="page-item <?= $current <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link border-0 text-secondary" href="<?= $buildUrl($max = max(1, $current - 1)) ?>" aria-label="Anterior">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Numbers -->
                            <?php if ($start > 1): ?>
                                <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                            <?php endif; ?>

                            <?php for ($p = $start; $p <= $end; $p++): ?>
                                <li class="page-item <?= $p === $current ? 'active' : '' ?>">
                                    <a class="page-link <?= $p === $current ? 'bg-primary border-primary text-white fw-bold' : 'border-0 text-dark hover-bg-light' ?>" 
                                       href="<?= $buildUrl($p) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end < $pages): ?>
                                <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                            <?php endif; ?>

                            <!-- Next / Last -->
                            <li class="page-item <?= $current >= $pages ? 'disabled' : '' ?>">
                                <a class="page-link border-0 text-secondary" href="<?= $buildUrl(min($pages, $current + 1)) ?>" aria-label="Próxima">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                            <li class="page-item <?= $current >= $pages ? 'disabled' : '' ?>">
                                <a class="page-link border-0 text-secondary" href="<?= $buildUrl($pages) ?>" aria-label="Última">
                                    <i class="bi bi-chevron-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
