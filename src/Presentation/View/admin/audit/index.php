<?php

/** @var array $logs */
/** @var array $filters */
/** @var int $page */
/** @var int $perPage */
/** @var int $total */

$totalPages = max(1, (int)ceil($total / $perPage));
?>
<h1 class="h4 mb-3">Auditoria do sistema</h1>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="get" action="/admin/audit-logs">
            <div class="col-md-2">
                <label class="form-label form-label-sm">Tipo de ator</label>
                <select name="actor_type" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="ADMIN" <?= $filters['actor_type'] === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                    <option value="PORTAL_USER" <?= $filters['actor_type'] === 'PORTAL_USER' ? 'selected' : '' ?>>Portal</option>
                    <option value="SYSTEM" <?= $filters['actor_type'] === 'SYSTEM' ? 'selected' : '' ?>>Sistema</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Contexto</label>
                <input type="text" name="context_type"
                    value="<?= htmlspecialchars($filters['context_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control form-control-sm"
                    placeholder="ex: submission">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Ação</label>
                <input type="text" name="action"
                    value="<?= htmlspecialchars($filters['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control form-control-sm"
                    placeholder="ex: SUBMISSION_STATUS_CHANGED">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Busca</label>
                <input type="text" name="search"
                    value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="form-control form-control-sm"
                    placeholder="Resumo ou nome">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-sm btn-primary w-100" type="submit">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!$logs): ?>
            <p class="text-muted mb-0">Nenhum registro de auditoria encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Data / Hora</th>
                            <th>Ação</th>
                            <th>Ator</th>
                            <th>Contexto</th>
                            <th>Resumo</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['occurred_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><code class="small"><?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td>
                                    <span class="badge bg-light text-muted border me-1">
                                        <?= htmlspecialchars($log['actor_type'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?= htmlspecialchars($log['actor_name'] ?? (string)$log['actor_id'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php if ($log['context_type']): ?>
                                        <span class="small">
                                            <?= htmlspecialchars($log['context_type'], ENT_QUOTES, 'UTF-8') ?>
                                            #<?= (int)$log['context_id'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">–</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($log['summary'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-2">
                    <ul class="pagination pagination-sm mb-0">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>