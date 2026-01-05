<?php
use App\Support\Auth;

/** @var array $filters */
/** @var array $rows */
/** @var array $types */
/** @var array $statuses */
/** @var string $csrfToken */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Fila de notificações (Outbox)</h1>
        <p class="text-muted small mb-0">
            Monitoramento dos envios de e-mail (Graph). Máx. 200 registros por consulta.
        </p>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-2">
                <label class="form-label small mb-1">De</label>
                <input type="date" name="from_date" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Até</label>
                <input type="date" name="to_date" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($statuses as $st): ?>
                        <option value="<?= htmlspecialchars($st) ?>" <?= (($filters['status'] ?? '') === $st) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Tipo</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($types as $tp): ?>
                        <option value="<?= htmlspecialchars($tp) ?>" <?= (($filters['type'] ?? '') === $tp) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tp) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">E-mail</label>
                <input type="text" name="email" class="form-control form-control-sm"
                    placeholder="destinatario@..."
                    value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!$rows): ?>
            <p class="text-muted small mb-0">Nenhum registro encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Tipo</th>
                            <th>Destinatário</th>
                            <th>Assunto</th>
                            <th class="text-end">Tentativas</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $status = $r['status'] ?? '';
                            $badge = 'bg-secondary';
                            if ($status === 'PENDING') $badge = 'bg-warning text-dark';
                            if ($status === 'SENDING') $badge = 'bg-info text-dark';
                            if ($status === 'SENT')    $badge = 'bg-success';
                            if ($status === 'FAILED')  $badge = 'bg-danger';
                            if ($status === 'CANCELLED') $badge = 'bg-secondary';
                            ?>
                            <tr>
                                <td class="small">#<?= (int)$r['id'] ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
                                <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                                <td class="small"><code><?= htmlspecialchars($r['type'] ?? '') ?></code></td>
                                <td class="small">
                                    <?= htmlspecialchars($r['recipient_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="small">
                                    <a href="/admin/notifications/outbox/<?= (int)$r['id'] ?>">
                                        <?= htmlspecialchars($r['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td class="small text-end">
                                    <?= (int)($r['attempts'] ?? 0) ?>/<?= (int)($r['max_attempts'] ?? 5) ?>
                                </td>

                                <td class="text-end">
                                    <?php if (($r['status'] ?? '') === 'FAILED'): ?>
                                        <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/reprocess" class="d-inline">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button class="btn btn-sm btn-outline-primary">Reprocessar</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (($r['status'] ?? '') === 'PENDING'): ?>
                                        <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/cancel" class="d-inline">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (in_array(($r['status'] ?? ''), ['FAILED', 'CANCELLED'], true) && Auth::hasRole('SUPER_ADMIN')): ?>
                                        <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/reset" class="d-inline">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button class="btn btn-sm btn-outline-secondary">Reset</button>
                                        </form>
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