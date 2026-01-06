<?php
/** @var array $pagination */
/** @var string $csrfToken */
$items = $pagination['items'] ?? [];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-lock-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Registros de Auditoria</h1>
            <p class="text-muted mb-0 small">Histórico detalhado de segurança e ações do sistema</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-search" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Pesquisar Auditoria</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3" method="get" action="/admin/audit-logs">
            <div class="col-md-3">
                <label class="nd-label">Tipo de Ator</label>
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
                <label class="nd-label">Ação</label>
                <div class="nd-input-group">
                    <input type="text" name="action"
                        value="<?= htmlspecialchars($_GET['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="nd-input"
                        placeholder="Ex: LOGIN_SUCCESS"
                        style="padding-left: 2.5rem;">
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
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="nd-card">
    <div class="nd-card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
             <i class="bi bi-list-check" style="color: var(--nd-navy-500);"></i>
             <h5 class="nd-card-title mb-0">Logs de Atividade</h5>
        </div>
        <span class="badge bg-light text-dark border">Página <?= (int)$pagination['page'] ?> de <?= (int)($pagination['pages'] ?? 1) ?></span>
    </div>

    <div class="nd-card-body p-0">
         <?php if (!$items): ?>
             <div class="text-center py-5">
                <i class="bi bi-clipboard-x text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum registro de auditoria encontrado.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                         <tr>
                            <th style="width: 180px;">Data</th>
                            <th>Ação</th>
                            <th>Ator</th>
                            <th>Alvo</th>
                            <th>Detalhes (JSON)</th>
                            <th class="text-end">IP</th>
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
                                        
                                        $actionMap = [
                                            'LOGIN_SUCCESS' => 'Autenticação concluída com êxito',
                                            'LOGIN_FAILED' => 'Falha no processo de autenticação',
                                            'PORTAL_USER_CREATED' => 'Usuário do portal criado',
                                            'PORTAL_USER_UPDATED' => 'Dados do usuário do portal atualizados',
                                            'PORTAL_ACCESS_LINK_GENERATED' => 'Link de acesso ao portal gerado',
                                            'PORTAL_LOGIN_CODE_FAILED' => 'Falha na validação do código de acesso ao portal',
                                            'PORTAL_LOGIN_SUCCESS_CODE' => 'Autenticação via código de acesso ao portal concluída',
                                            'SUBMISSION_RESPONSE_FILES_UPLOADED' => 'Arquivos de resposta da submissão enviados',
                                            'PORTAL_SUBMISSION_CREATED' => 'Submissão criada no portal',
                                            'SUBMISSION_CREATED' => 'Submissão criada'
                                        ];

                                        $displayAction = $actionMap[$action] ?? $action;

                                        // Default fallback
                                        $style = '';
                                        $badgeClass = 'bg-light text-dark border'; 

                                        // Specific color mapping (Unique colors for each)
                                        switch ($action) {
                                            case 'LOGIN_SUCCESS':
                                                $badgeClass = 'nd-badge-success'; // Green
                                                break;
                                            
                                            case 'LOGIN_FAILED':
                                                $badgeClass = 'nd-badge-danger'; // Red
                                                break;

                                            case 'PORTAL_USER_CREATED':
                                                $badgeClass = 'bg-primary text-white'; // Blue
                                                break;
                                            
                                            case 'PORTAL_USER_UPDATED':
                                                $badgeClass = 'bg-info text-dark bg-opacity-25 border-info border-opacity-25'; // Soft Cyan
                                                break;

                                            case 'PORTAL_ACCESS_LINK_GENERATED':
                                                $badgeClass = 'text-white';
                                                $style = 'background-color: #6f42c1;'; // Purple
                                                break;

                                            case 'PORTAL_LOGIN_CODE_FAILED':
                                                $badgeClass = 'text-white';
                                                $style = 'background-color: #fd7e14;'; // Orange
                                                break;
                                                
                                            case 'PORTAL_LOGIN_SUCCESS_CODE':
                                                $badgeClass = 'text-white';
                                                $style = 'background-color: #20c997;'; // Teal
                                                break;

                                            case 'SUBMISSION_RESPONSE_FILES_UPLOADED':
                                                $badgeClass = 'bg-warning text-dark bg-opacity-25 border-warning border-opacity-25'; // Soft Yellow
                                                break;

                                            case 'PORTAL_SUBMISSION_CREATED':
                                                $badgeClass = 'text-white';
                                                $style = 'background-color: #d63384;'; // Pink
                                                break;
                                                
                                            case 'SUBMISSION_CREATED':
                                                $badgeClass = 'text-white';
                                                $style = 'background-color: #6610f2;'; // Indigo
                                                break;
                                            
                                            default:
                                                // Fallback logic for unmapped system codes
                                                if (str_contains($action, '_SUCCESS')) $badgeClass = 'nd-badge-success-soft';
                                                elseif (str_contains($action, '_FAILED')) $badgeClass = 'nd-badge-danger-soft';
                                                elseif (str_contains($action, 'DELETE')) $badgeClass = 'bg-danger text-white';
                                                elseif (str_contains($action, 'CREATE')) $badgeClass = 'bg-success text-white';
                                                elseif (str_contains($action, 'UPDATE')) $badgeClass = 'bg-info text-white';
                                                break;
                                        }
                                    ?>
                                    <span class="badge fw-normal <?= $badgeClass ?>" style="<?= $style ?>">
                                        <?= htmlspecialchars($displayAction, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($log['actor_type'] ?? 'Sistema', ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if (!empty($log['actor_id'])): ?>
                                             <span class="text-muted">ID: #<?= (int)$log['actor_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                         <span class="fw-medium text-dark"><?= htmlspecialchars($log['target_type'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                          <?php if (!empty($log['target_id'])): ?>
                                             <span class="text-muted">#<?= (int)$log['target_id'] ?></span>
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
                <div class="d-flex justify-content-center border-top p-3">
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php $current = (int)$pagination['page']; ?>
                            <?php if ($current > 1): ?>
                                <li class="page-item">
                                    <a class="page-link border-0 text-dark" href="/admin/audit-logs?page=<?= $current - 1 ?>">&laquo; Anterior</a>
                                </li>
                            <?php endif; ?>
                            
                            <li class="page-item disabled">
                                <span class="page-link border-0 text-muted">Página <?= $current ?></span>
                            </li>

                            <?php if ($current < (int)$pagination['pages']): ?>
                                <li class="page-item">
                                    <a class="page-link border-0 text-dark" href="/admin/audit-logs?page=<?= $current + 1 ?>">Próxima &raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
