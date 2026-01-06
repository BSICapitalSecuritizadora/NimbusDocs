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
                                        $badgeClass = 'bg-light text-dark border'; 
                                        
                                        if (str_contains($action, '_SUCCESS')) $badgeClass = 'nd-badge-success';
                                        elseif (str_contains($action, '_FAILED')) $badgeClass = 'nd-badge-danger';
                                        elseif (str_contains($action, 'DELETE')) $badgeClass = 'nd-badge-danger-soft';
                                        elseif (str_contains($action, 'CREATE')) $badgeClass = 'nd-badge-success-soft';
                                        elseif (str_contains($action, 'UPDATE')) $badgeClass = 'bg-info text-white border-info';
                                    ?>
                                    <span class="badge font-monospace fw-normal <?= $badgeClass ?>">
                                        <?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>
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
