<?php

/** @var array $row */
/** @var array $payload */
/** @var string $csrfToken */
$status = $row['status'] ?? '';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-envelope-open-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes da Notificação</h1>
            <p class="text-muted mb-0 small">Visualizando registro #<?= (int)$row['id'] ?> (<?= htmlspecialchars($row['type'] ?? '') ?>)</p>
        </div>
    </div>
    <a href="/admin/notifications/outbox" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-7">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Resumo</h5>
            </div>
            <div class="nd-card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Status</div>
                    <div class="col-sm-9">
                        <?php
                            $badge = 'nd-badge-secondary';
                            $icon = '';
                            $label = $status;
                            
                            switch ($status) {
                                case 'PENDING':
                                    $badge = 'nd-badge-warning'; // Usando classe padrão se existir, ou mantendo estilo manual
                                    $badge = 'bg-warning text-dark border-warning'; // Mantendo estilo visual do usuário por segurança
                                    $icon = 'bi-clock';
                                    $label = 'Pendente';
                                    break;
                                case 'SENDING':
                                    $badge = 'bg-info text-white border-info';
                                    $icon = 'bi-arrow-repeat';
                                    $label = 'Enviando';
                                    break;
                                case 'SENT':
                                    $badge = 'nd-badge-success';
                                    $icon = 'bi-check-all';
                                    $label = 'Enviado';
                                    break;
                                case 'FAILED':
                                    $badge = 'nd-badge-danger';
                                    $icon = 'bi-exclamation-octagon';
                                    $label = 'Falha';
                                    break;
                            }
                        ?>
                        <span class="nd-badge <?= $badge ?>">
                            <i class="bi <?= $icon ?> me-1"></i> <?= htmlspecialchars($label) ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Destinatário</div>
                    <div class="col-sm-9 text-dark fw-medium">
                        <?= htmlspecialchars($row['recipient_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Assunto</div>
                    <div class="col-sm-9 text-dark">
                        <?= htmlspecialchars($row['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Template</div>
                    <div class="col-sm-9">
                        <?php
                            $templateName = $row['template'] ?? '';
                            $templateLabel = match($templateName) {
                                'token_created' => 'Criação de Token',
                                'password_reset' => 'Redefinição de Senha',
                                'welcome_email' => 'Boas-vindas',
                                default => ucwords(str_replace('_', ' ', $templateName))
                            };
                        ?>
                        <code class="px-2 py-1 rounded bg-light border" style="color: var(--nd-navy-600);"><?= htmlspecialchars($templateLabel, ENT_QUOTES, 'UTF-8') ?></code>
                        <?php if ($templateName !== $templateLabel): ?>
                            <small class="text-muted ms-2">(<?= htmlspecialchars($templateName) ?>)</small>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-3" style="border-color: var(--nd-gray-200);">

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Tentativas</div>
                    <div class="col-sm-9 text-dark">
                         <?= (int)($row['attempts'] ?? 0) ?> <span class="text-muted">de</span> <?= (int)($row['max_attempts'] ?? 5) ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Próxima</div>
                    <div class="col-sm-9 text-dark">
                        <i class="bi bi-calendar-event me-1 text-muted"></i>
                        <?= htmlspecialchars($row['next_attempt_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-sm-3 text-muted small text-uppercase fw-bold pt-1">Enviado em</div>
                    <div class="col-sm-9 text-dark">
                        <i class="bi bi-send me-1 text-muted"></i>
                        <?= htmlspecialchars($row['sent_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($row['last_error'])): ?>
            <div class="nd-card mb-4 border-danger">
                <div class="nd-card-header bg-danger text-white d-flex align-items-center gap-2">
                    <i class="bi bi-bug-fill"></i>
                    <h5 class="nd-card-title mb-0 text-white">Último Erro Registrado</h5>
                </div>
                <div class="nd-card-body bg-light">
                     <pre class="small mb-0 p-3 rounded bg-white border border-danger text-danger font-monospace" style="white-space: pre-wrap;"><?= htmlspecialchars((string)$row['last_error']) ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex gap-2">
            <?php if ($status === 'FAILED'): ?>
                <form method="post" action="/admin/notifications/outbox/<?= (int)$row['id'] ?>/reprocess">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="nd-btn nd-btn-primary">
                        <i class="bi bi-arrow-repeat me-1"></i> Reprocessar Envio
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($status === 'PENDING'): ?>
                <form method="post" action="/admin/notifications/outbox/<?= (int)$row['id'] ?>/cancel">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="nd-btn nd-btn-outline text-danger border-danger">
                        <i class="bi bi-x-circle me-1"></i> Cancelar Envio
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-code-square" style="color: var(--nd-navy-500);"></i>
                <h5 class="nd-card-title mb-0">Payload (JSON)</h5>
            </div>
            <div class="nd-card-body p-0">
                <pre class="m-0 p-3 small font-monospace text-muted" style="white-space: pre-wrap; background-color: #fafbfc; min-height: 100%; border-bottom-left-radius: var(--nd-radius-md); border-bottom-right-radius: var(--nd-radius-md);"><?= htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
            </div>
        </div>
    </div>
</div>