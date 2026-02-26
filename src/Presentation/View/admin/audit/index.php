<?php

/** @var array $logs */
/** @var array $filters */
/** @var int $page */
/** @var int $perPage */
/** @var int $total */

$totalPages = max(1, (int)ceil($total / $perPage));
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-check text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Trilha de Auditoria</h1>
            <p class="text-muted mb-0 small">Registro histórico de todas as operações realizadas no sistema.</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros de Pesquisa</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3" method="get" action="/admin/audit-logs">
            <div class="col-md-3">
                <label class="nd-label">Tipo de Usuário</label>
                <div class="nd-input-group">
                    <select name="actor_type" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todos</option>
                        <option value="ADMIN" <?= $filters['actor_type'] === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
                        <option value="PORTAL_USER" <?= $filters['actor_type'] === 'PORTAL_USER' ? 'selected' : '' ?>>Usuário Portal</option>
                        <option value="SYSTEM" <?= $filters['actor_type'] === 'SYSTEM' ? 'selected' : '' ?>>Sistema</option>
                    </select>
                    <i class="bi bi-person-badge nd-input-icon"></i>
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="nd-label">Atividade</label>
                <div class="nd-input-group">
                    <input type="text" name="action"
                        value="<?= htmlspecialchars($filters['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="nd-input"
                        placeholder="Ex: Login, Atualização..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-lightning nd-input-icon"></i>
                </div>
            </div>

             <div class="col-md-3">
                <label class="nd-label">Objeto Afetado (Opcional)</label>
                <input type="text" name="context_type"
                    value="<?= htmlspecialchars($filters['context_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="nd-input"
                    placeholder="Ex: Submissão, Usuário...">
            </div>

            <div class="col-md-3">
                <label class="nd-label">Busca Geral</label>
                <div class="nd-input-group">
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="nd-input"
                        placeholder="Resumo, Nome..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button class="nd-btn nd-btn-primary" type="submit">
                    <i class="bi bi-filter me-1"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<div class="nd-card">
    <div class="nd-card-header d-flex justify-content-between align-items-center">
         <div class="d-flex align-items-center gap-2">
            <i class="bi bi-list-columns" style="color: var(--nd-navy-500);"></i>
            <h5 class="nd-card-title mb-0">Registros de Atividade</h5>
        </div>
        <span class="badge bg-light text-dark border">Total: <?= (int)$total ?></span>
    </div>
    <div class="nd-card-body p-0">
        <?php if (!$logs): ?>
            <div class="text-center py-5">
                <i class="bi bi-shield-slash text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum registro de auditoria encontrado para os filtros selecionados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Data da Ocorrência</th>
                            <th>Ação Registrada</th>
                            <th>Responsável</th>
                            <th>Detalhes da Operação</th>
                            <th>Objeto</th>
                            <th class="text-end">Endereço IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($log['occurred_at'])), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $action = $log['action'];
                                        $label = $action;
                                        $badgeClass = 'bg-light text-dark border'; 
                                        
                                        // Friendly mappings
                                        if (str_contains($action, 'LOGIN_SUCCESS')) {
                                            $badgeClass = 'nd-badge-success';
                                            $label = 'Acesso Realizado';
                                        }
                                        elseif (str_contains($action, 'LOGIN_FAILED')) {
                                            $badgeClass = 'nd-badge-danger';
                                            $label = 'Falha de Acesso';
                                        }
                                        elseif (str_contains($action, 'LOGOUT')) {
                                            $badgeClass = 'nd-badge-secondary';
                                            $label = 'Saída do Sistema';
                                        }
                                        // Specific user requested mappings
                                        elseif ($action === 'Usuário Submitted Corrections' || str_contains($action, 'Submitted Corrections')) {
                                            $badgeClass = 'bg-primary text-white border-0';
                                            $label = 'Correções Enviadas';
                                        }
                                        elseif ($action === 'Usuário Uploaded Correction Arquivo' || str_contains($action, 'Uploaded Correction')) {
                                            $badgeClass = 'bg-info text-dark border-0';
                                            $label = 'Arquivo de Correção Env.';
                                        }
                                        elseif ($action === 'Submissão Notification Resent' || str_contains($action, 'Notification Resent')) {
                                            $badgeClass = 'bg-warning text-dark border-0';
                                            $label = 'Notificação Reenviada';
                                        }
                                        elseif ($action === 'Visualização de Arquivo' || str_contains($action, 'Visualização')) {
                                            $badgeClass = 'bg-secondary text-white border-0';
                                            $label = 'Visualização de Arquivo';
                                        }
                                        elseif ($action === 'Usuário Portal Criado' || str_contains($action, 'Portal Criado')) {
                                            $badgeClass = 'bg-success text-white border-0';
                                            $label = 'Usuário Portal Criado';
                                        }
                                        elseif ($action === 'Alteração de Status') {
                                            $badgeClass = 'nd-badge-primary border-0';
                                            $label = 'Alteração de Status';
                                        }
                                        // General mappings fallback
                                        elseif (str_contains($action, 'CREATE') || str_contains($action, 'STORE')) {
                                            $badgeClass = 'bg-success text-white';
                                            $label = 'Criação';
                                        }
                                        elseif (str_contains($action, 'UPDATE') || str_contains($action, 'EDIT')) {
                                            $badgeClass = 'bg-warning text-dark';
                                            $label = 'Atualização';
                                        }
                                        elseif (str_contains($action, 'DELETE') || str_contains($action, 'DESTROY')) {
                                            $badgeClass = 'bg-danger text-white';
                                            $label = 'Remoção';
                                        }
                                        elseif (str_contains($action, 'DOWNLOAD')) {
                                            $badgeClass = 'bg-info text-white';
                                            $label = 'Download';
                                        }
                                    ?>
                                    <span class="badge font-monospace fw-normal <?= $badgeClass ?>" title="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($log['actor_type'] === 'ADMIN'): ?>
                                            <i class="bi bi-person-shield text-primary" title="Administrador"></i>
                                        <?php elseif ($log['actor_type'] === 'PORTAL_USER'): ?>
                                            <i class="bi bi-person-circle text-muted" title="Usuário"></i>
                                        <?php else: ?>
                                            <i class="bi bi-hdd-rack text-secondary" title="Sistema"></i>
                                        <?php endif; ?>
                                        <span class="fw-medium text-dark small">
                                            <?= htmlspecialchars($log['actor_name'] ?? ('ID: ' . $log['actor_id']), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <?= htmlspecialchars($log['summary'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($log['context_type']): ?>
                                        <?php
                                            $ct = $log['context_type'];
                                            $ctLabel = $ct;
                                            $ctBadge = 'bg-light text-secondary border';
                                            $ctIdClass = 'text-dark fw-bold ms-1';
                                            
                                            // Unify submission labels and colors
                                            if (strtolower($ct) === 'submission' || mb_strtolower($ct) === 'submissão') {
                                                $ctLabel = 'Submissão';
                                                $ctBadge = 'text-white border-0';
                                                $ctIdClass = 'text-white-50 ms-1 fw-bold';
                                            } elseif (strtolower($ct) === 'admin_user' || strtolower($ct) === 'administrador') {
                                                $ctLabel = 'Administrador';
                                                $ctBadge = 'bg-primary text-white border-0';
                                                $ctIdClass = 'text-white-50 ms-1 fw-bold';
                                            } elseif (strtolower($ct) === 'portal_user' || strtolower($ct) === 'usuário') {
                                                $ctLabel = 'Usuário Portal';
                                                $ctBadge = 'bg-info text-dark border-0';
                                                $ctIdClass = 'text-black-50 ms-1 fw-bold';
                                            }
                                        ?>
                                        <?php if ($ctLabel === 'Submissão'): ?>
                                            <span class="badge <?= $ctBadge ?> fw-normal small" style="background-color: #6610f2;">
                                                <?= htmlspecialchars($ctLabel, ENT_QUOTES, 'UTF-8') ?>
                                                <span class="<?= $ctIdClass ?>">#<?= (int)$log['context_id'] ?></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge <?= $ctBadge ?> fw-normal small">
                                                <?= htmlspecialchars($ctLabel, ENT_QUOTES, 'UTF-8') ?>
                                                <span class="<?= $ctIdClass ?>">#<?= (int)$log['context_id'] ?></span>
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">–</span>
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

            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center border-top p-3">
                    <nav aria-label="Navegação da auditoria">
                        <ul class="pagination pagination-sm mb-0">
                            <?php 
                            // Simple pagination logic for display
                            $range = 2;
                            $start = max(1, $page - $range);
                            $end = min($totalPages, $page + $range);
                            ?>
                            
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link border-0 text-dark" href="?page=1&<?= http_build_query($filters) ?>">&laquo;</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($p = $start; $p <= $end; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                    <a class="page-link <?= $p === $page ? 'bg-primary border-primary text-white' : 'border-0 text-dark' ?>" 
                                       href="?page=<?= $p ?>&<?= http_build_query($filters) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link border-0 text-dark" href="?page=<?= $totalPages ?>&<?= http_build_query($filters) ?>">&raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>