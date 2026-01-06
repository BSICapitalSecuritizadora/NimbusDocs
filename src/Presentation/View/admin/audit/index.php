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
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Auditoria do Sistema</h1>
            <p class="text-muted mb-0 small">Rastreabilidade completa de ações e segurança</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros Avançados</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3" method="get" action="/admin/audit-logs">
            <div class="col-md-3">
                <label class="nd-label">Tipo de Ator</label>
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
                <label class="nd-label">Ação</label>
                <div class="nd-input-group">
                    <input type="text" name="action"
                        value="<?= htmlspecialchars($filters['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="nd-input"
                        placeholder="Ex: LOGIN_SUCCESS"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-lightning nd-input-icon"></i>
                </div>
            </div>

             <div class="col-md-3">
                <label class="nd-label">Contexto (Opcional)</label>
                <input type="text" name="context_type"
                    value="<?= htmlspecialchars($filters['context_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="nd-input"
                    placeholder="Ex: submission">
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
            <h5 class="nd-card-title mb-0">Registros de Auditoria</h5>
        </div>
        <span class="badge bg-light text-dark border">Total: <?= (int)$total ?></span>
    </div>
    <div class="nd-card-body p-0">
        <?php if (!$logs): ?>
            <div class="text-center py-5">
                <i class="bi bi-shield-slash text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum registro de auditoria encontrado.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Data / Hora</th>
                            <th>Ação</th>
                            <th>Ator</th>
                            <th>Detalhes</th>
                            <th>Contexto</th>
                            <th class="text-end">IP</th>
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
                                        $badgeClass = 'bg-light text-dark border'; // Default
                                        
                                        if (str_contains($action, '_SUCCESS')) $badgeClass = 'nd-badge-success-soft'; // Soft green custom class or similar
                                        elseif (str_contains($action, '_FAILED')) $badgeClass = 'nd-badge-danger-soft';
                                        elseif (str_contains($action, 'DELETE')) $badgeClass = 'bg-danger text-white';
                                        elseif (str_contains($action, 'CREATE')) $badgeClass = 'bg-primary text-white';
                                        elseif (str_contains($action, 'UPDATE')) $badgeClass = 'bg-info text-dark';
                                        
                                        // Specific overrides based on user image perception (pink text for success/fail?)
                                        // Let's make it look premium readable:
                                        if ($action === 'LOGIN_SUCCESS') $badgeClass = 'nd-badge-success';
                                        if ($action === 'LOGIN_FAILED') $badgeClass = 'nd-badge-danger';
                                    ?>
                                    <span class="badge font-monospace fw-normal <?= $badgeClass ?>">
                                        <?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>
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
                                        <span class="badge bg-light text-secondary border fw-normal small">
                                            <?= htmlspecialchars($log['context_type'], ENT_QUOTES, 'UTF-8') ?>
                                            <span class="text-dark fw-bold ms-1">#<?= (int)$log['context_id'] ?></span>
                                        </span>
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