<?php
/** @var array $items */
/** @var array $filters */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Fila de notificações</h1>
        <p class="text-muted small mb-0">Visualize e gerencie o outbox de e-mails.</p>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success py-2 small"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="get" action="/admin/notifications/outbox">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <?php $statuses = ['', 'PENDING','SENDING','SENT','FAILED','CANCELLED']; ?>
                    <?php foreach ($statuses as $st): ?>
                        <option value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>>
                            <?= $st === '' ? 'Todos' : $st ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Destinatário</label>
                <input type="text" name="recipient" value="<?= htmlspecialchars($filters['recipient'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm" placeholder="email@dominio.com">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Tipo</label>
                <input type="text" name="type" value="<?= htmlspecialchars($filters['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm" placeholder="NEW_GENERAL_DOCUMENT">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-primary mt-auto" type="submit">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a class="btn btn-sm btn-outline-secondary mt-auto" href="/admin/notifications/outbox">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!$items): ?>
            <p class="text-muted small mb-0">Nenhum item encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Tipo</th>
                            <th>Destinatário</th>
                            <th>Assunto</th>
                            <th>Tentativas</th>
                            <th>Próxima tentativa</th>
                            <th>Erro</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $status = $item['status'];
                            $badge = 'bg-secondary';
                            if ($status === 'PENDING') $badge = 'bg-info text-dark';
                            if ($status === 'SENDING') $badge = 'bg-primary';
                            if ($status === 'SENT') $badge = 'bg-success';
                            if ($status === 'FAILED') $badge = 'bg-danger';
                            if ($status === 'CANCELLED') $badge = 'bg-secondary';
                            ?>
                            <tr>
                                <td>#<?= (int)$item['id'] ?></td>
                                <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td class="small text-muted"><?= htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="small">
                                    <?= htmlspecialchars($item['recipient_email'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($item['recipient_name'])): ?>
                                        <div class="text-muted"><?= htmlspecialchars($item['recipient_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="small"><?= htmlspecialchars($item['subject'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="small">
                                    <?= (int)$item['attempts'] ?>/<?= (int)$item['max_attempts'] ?>
                                </td>
                                <td class="small text-muted">
                                    <?= htmlspecialchars($item['next_attempt_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="small text-muted" style="max-width:220px;">
                                    <?= htmlspecialchars($item['last_error'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($status === 'FAILED'): ?>
                                        <form method="post" action="/admin/notifications/outbox/<?= (int)$item['id'] ?>/reprocess" class="d-inline">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <button class="btn btn-sm btn-outline-primary" type="submit">
                                                <i class="bi bi-arrow-clockwise"></i> Reprocessar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($status === 'PENDING'): ?>
                                        <form method="post" action="/admin/notifications/outbox/<?= (int)$item['id'] ?>/cancel" class="d-inline" onsubmit="return confirm('Cancelar este envio pendente?');">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($status !== 'FAILED' && $status !== 'PENDING'): ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
