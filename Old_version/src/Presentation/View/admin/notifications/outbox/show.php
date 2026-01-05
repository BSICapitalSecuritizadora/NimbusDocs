<?php

/** @var array $row */
/** @var array $payload */
/** @var string $csrfToken */
$status = $row['status'] ?? '';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h5 mb-1">Notificação #<?= (int)$row['id'] ?></h1>
        <div class="small text-muted">
            Criada em <?= htmlspecialchars($row['created_at'] ?? '') ?> · Tipo <code><?= htmlspecialchars($row['type'] ?? '') ?></code>
        </div>
    </div>
    <a href="/admin/notifications/outbox" class="btn btn-sm btn-outline-secondary">Voltar</a>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 mb-3">Resumo</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-3 small text-muted">Status</dt>
                    <dd class="col-sm-9 small"><strong><?= htmlspecialchars($status) ?></strong></dd>

                    <dt class="col-sm-3 small text-muted">Para</dt>
                    <dd class="col-sm-9 small"><?= htmlspecialchars($row['recipient_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-3 small text-muted">Assunto</dt>
                    <dd class="col-sm-9 small"><?= htmlspecialchars($row['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-3 small text-muted">Template</dt>
                    <dd class="col-sm-9 small"><code><?= htmlspecialchars($row['template'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></dd>

                    <dt class="col-sm-3 small text-muted">Attempts</dt>
                    <dd class="col-sm-9 small"><?= (int)($row['attempts'] ?? 0) ?>/<?= (int)($row['max_attempts'] ?? 5) ?></dd>

                    <dt class="col-sm-3 small text-muted">Próxima tentativa</dt>
                    <dd class="col-sm-9 small"><?= htmlspecialchars($row['next_attempt_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-3 small text-muted">Enviado em</dt>
                    <dd class="col-sm-9 small"><?= htmlspecialchars($row['sent_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
            </div>
        </div>

        <?php if (!empty($row['last_error'])): ?>
            <div class="card mt-3 border-danger">
                <div class="card-body">
                    <h2 class="h6 mb-2 text-danger">Último erro</h2>
                    <pre class="small mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars((string)$row['last_error']) ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <?php if ($status === 'FAILED'): ?>
                <form method="post" action="/admin/notifications/outbox/<?= (int)$row['id'] ?>/reprocess" class="d-inline">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="btn btn-sm btn-primary">Reprocessar</button>
                </form>
            <?php endif; ?>

            <?php if ($status === 'PENDING'): ?>
                <form method="post" action="/admin/notifications/outbox/<?= (int)$row['id'] ?>/cancel" class="d-inline">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 mb-3">Payload (JSON)</h2>
                <pre class="small mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
            </div>
        </div>
    </div>
</div>