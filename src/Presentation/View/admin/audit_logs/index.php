<?php
/** @var array $pagination */
/** @var string $csrfToken */
$items = $pagination['items'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Auditoria do Sistema</h1>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Ação</th>
                        <th>Ator</th>
                        <th>Alvo</th>
                        <th>IP</th>
                        <th>Contexto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Nenhum registro de auditoria encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $log): ?>
                            <tr>
                                <td class="text-nowrap"><?= htmlspecialchars($log['occurred_at'] ?? $log['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><code><?= htmlspecialchars($log['action'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td>
                                    <?= htmlspecialchars($log['actor_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($log['actor_id'])): ?>
                                        #<?= (int)$log['actor_id'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($log['target_type'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($log['target_id'])): ?>
                                        #<?= (int)$log['target_id'] ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="small text-break">
                                    <?php if (!empty($log['details'])): ?>
                                        <code><?= htmlspecialchars(mb_substr($log['details'], 0, 100), ENT_QUOTES, 'UTF-8') ?></code>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (($pagination['pages'] ?? 1) > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Página <?= (int)$pagination['page'] ?> de <?= (int)$pagination['pages'] ?>
            </div>
            <div>
                <?php $current = (int)$pagination['page']; ?>
                <?php if ($current > 1): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="/admin/audit-logs?page=<?= $current - 1 ?>">Anterior</a>
                <?php endif; ?>
                <?php if ($current < (int)$pagination['pages']): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="/admin/audit-logs?page=<?= $current + 1 ?>">Próxima</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
