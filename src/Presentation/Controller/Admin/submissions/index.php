<?php

/** @var array $pagination */
/** @var array $filters */
/** @var string $csrfToken */
/** @var array $flash */

$items = $pagination['items'] ?? [];
$page = $pagination['page'] ?? 1;
$pages = $pagination['pages'] ?? 1;
$success = $flash['success'] ?? null;
$error = $flash['error'] ?? null;

$statusFilter = $filters['status'] ?? '';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Submissões do Portal</h1>
</div>

<?php if ($success): ?>
    <div class="alert alert-success py-2">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger py-2">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<form class="row g-2 mb-3" method="get" action="/admin/submissions">
    <div class="col-md-3">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="">Todos</option>
            <option value="PENDING" <?= $statusFilter === 'PENDING' ? 'selected' : '' ?>>Pendente</option>
            <option value="UNDER_REVIEW" <?= $statusFilter === 'UNDER_REVIEW' ? 'selected' : '' ?>>Em análise</option>
            <option value="COMPLETED" <?= $statusFilter === 'COMPLETED' ? 'selected' : '' ?>>Concluído</option>
            <option value="REJECTED" <?= $statusFilter === 'REJECTED' ? 'selected' : '' ?>>Rejeitado</option>
        </select>
    </div>
    <div class="col-md-3 align-self-end">
        <button type="submit" class="btn btn-outline-primary btn-sm">
            Filtrar
        </button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Usuário</th>
                        <th>Status</th>
                        <th>Enviado em</th>
                        <th class="text-end">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Nenhuma submissão encontrada.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $s): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($s['reference_code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td><?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= htmlspecialchars($s['user_full_name'], ENT_QUOTES, 'UTF-8') ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($s['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td><?= htmlspecialchars($s['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($s['submitted_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end">
                                    <a href="/admin/submissions/<?= (int) $s['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $pages; $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="/admin/submissions?page=<?= $p ?>&status=<?= urlencode($statusFilter) ?>">
                        <?= $p ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>