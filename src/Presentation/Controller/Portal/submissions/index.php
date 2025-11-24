<?php

/** @var array $pagination */
/** @var array $flash */

$items = $pagination['items'] ?? [];
$page  = $pagination['page'] ?? 1;
$pages = $pagination['pages'] ?? 1;
$success = $flash['success'] ?? null;
$error   = $flash['error']   ?? null;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Minhas submissões</h1>
    <a href="/portal/submissions/create" class="btn btn-primary btn-sm">Nova submissão</a>
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

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Enviado em</th>
                        <th class="text-end">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Você ainda não possui submissões.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $s): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($s['reference_code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td><?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($s['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($s['submitted_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-secondary">
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
                    <a class="page-link" href="/portal/submissions?page=<?= $p ?>">
                        <?= $p ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>