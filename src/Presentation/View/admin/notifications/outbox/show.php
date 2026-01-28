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
            <i class="bi bi-search text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes do Envio</h1>
            <p class="text-muted mb-0 small">Protocolo de Auditoria #<?= (int)$row['id'] ?> &bull; Data do Evento: <?= date('d/m/Y', strtotime($row['created_at'])) ?></p>
        </div>
    </div>
    <a href="/admin/notifications/outbox" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar para Lista
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-7">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-card-heading" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Informações da Mensagem</h5>
            </div>
            <div class="nd-card-body">
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium pt-1">Status do Envio</div>
                    <div class="col-sm-9">
                        <?php
                            $badge = 'nd-badge-secondary';
                            $icon = '';
                            $label = $status;
                            
                            switch ($status) {
                                case 'PENDING':
                                    $badge = 'nd-badge-warning';
                                    $icon = 'bi-hourglass-split';
                                    $label = 'Aguardando Envio';
                                    break;
                                case 'SENDING':
                                    $badge = 'bg-info text-white border-info';
                                    $icon = 'bi-arrow-right-circle';
                                    $label = 'Em Processamento';
                                    break;
                                case 'SENT':
                                    $badge = 'nd-badge-success';
                                    $icon = 'bi-check-all';
                                    $label = 'Entregue com Sucesso';
                                    break;
                                case 'FAILED':
                                    $badge = 'nd-badge-danger';
                                    $icon = 'bi-x-circle';
                                    $label = 'Falha na Entrega';
                                    break;
                                case 'CANCELLED':
                                    $badge = 'bg-secondary text-white';
                                    $icon = 'bi-dash-circle';
                                    $label = 'Cancelado Manualmente';
                                    break;
                            }
                        ?>
                        <span class="nd-badge <?= $badge ?>">
                            <i class="bi <?= $icon ?> me-1"></i> <?= htmlspecialchars($label) ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium pt-1">Destinatário</div>
                    <div class="col-sm-9 text-dark">
                         <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope text-primary"></i>
                            <span class="fw-bold"><?= htmlspecialchars($row['recipient_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium pt-1">Assunto</div>
                    <div class="col-sm-9 text-dark">
                         <?= htmlspecialchars($row['subject'] ?? '(Sem assunto)', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-medium pt-1">Tipo de Evento</div>
                    <div class="col-sm-9">
                        <?php
                            $templateName = strtolower($row['template'] ?? '');
                            $templateInfo = match($templateName) {
                                'token_created'       => ['label' => 'Criação de Token',      'icon' => 'bi-key-fill',            'color' => 'var(--nd-primary-700)', 'bg' => 'var(--nd-primary-100)'],
                                'password_reset'      => ['label' => 'Redefinição de Senha',  'icon' => 'bi-shield-lock-fill',    'color' => 'var(--nd-danger-700)',  'bg' => 'var(--nd-danger-100)'],
                                'welcome_email'       => ['label' => 'Boas-vindas',           'icon' => 'bi-person-plus-fill',    'color' => 'var(--nd-success-700)', 'bg' => 'var(--nd-success-100)'],
                                'submission_received' => ['label' => 'Protocolo Recebido',    'icon' => 'bi-file-earmark-text',   'color' => 'var(--nd-navy-700)',    'bg' => 'var(--nd-navy-100)'],
                                'user_precreated'     => ['label' => 'Pré-cadastro de Usuário', 'icon' => 'bi-person-badge',    'color' => 'var(--nd-primary-700)', 'bg' => 'var(--nd-primary-100)'],
                                'new_announcement'    => ['label' => 'Novo Comunicado',       'icon' => 'bi-megaphone-fill',      'color' => 'var(--nd-gold-700)',    'bg' => 'var(--nd-gold-100)'],
                                'new_general_document'=> ['label' => 'Documento Publicado',   'icon' => 'bi-cloud-check-fill',    'color' => 'var(--nd-navy-700)',    'bg' => 'var(--nd-navy-100)'],
                                default               => ['label' => ucwords(str_replace(['_', '-'], ' ', $templateName)), 'icon' => 'bi-tag-fill', 'color' => 'var(--nd-gray-700)', 'bg' => 'var(--nd-gray-100)']
                            };
                        ?>
                        <div class="d-inline-flex align-items-center gap-2 rounded px-2 py-1" 
                             style="background: <?= $templateInfo['bg'] ?>; color: <?= $templateInfo['color'] ?>;">
                            <i class="bi <?= $templateInfo['icon'] ?>"></i>
                            <span class="fw-bold small"><?= htmlspecialchars(mb_strtoupper($templateInfo['label'])) ?></span>
                        </div>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <div class="row align-items-center">
                    <div class="col-6 mb-3 mb-md-0">
                         <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Tentativas de Envio</small>
                         <div class="d-flex align-items-center gap-2">
                            <span class="fs-5 fw-bold text-dark"><?= (int)($row['attempts'] ?? 0) ?></span>
                            <span class="text-muted small">de</span>
                            <span class="text-muted small"><?= (int)($row['max_attempts'] ?? 5) ?></span>
                         </div>
                    </div>
                    
                    <div class="col-6">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Data de Conclusão</small>
                        <div class="text-dark">
                            <?php if ($row['sent_at']): ?>
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                <?= date('d/m/Y H:i:s', strtotime($row['sent_at'])) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($row['last_error'])): ?>
            <div class="nd-card mb-4 border-danger">
                <div class="nd-card-header bg-danger text-white d-flex align-items-center gap-2">
                    <i class="bi bi-bug-fill"></i>
                    <h5 class="nd-card-title mb-0 text-white">Log de Erro Crítico</h5>
                </div>
                <div class="nd-card-body bg-light">
                     <div class="font-monospace text-danger small p-2"><?= htmlspecialchars((string)$row['last_error']) ?></div>
                     <div class="text-muted small mt-2">
                        <i class="bi bi-info-circle me-1"></i> Verifique se as credenciais do Microsoft Graph no <code>.env</code> estão atualizadas.
                     </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex gap-2">
            <?php if ($status === 'FAILED'): ?>
                <form method="post" action="/admin/notifications/outbox/<?= (int)$row['id'] ?>/reprocess">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="nd-btn nd-btn-primary">
                        <i class="bi bi-arrow-clockwise me-1"></i> Tentar Novamente
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($status === 'PENDING'): ?>
                <form method="post" action="/admin/notifications/outbox/<?= (int)$row['id'] ?>/cancel">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="nd-btn nd-btn-outline text-danger border-danger hover-danger-fill">
                        <i class="bi bi-stop-circle me-1"></i> Cancelar Envio
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payload Column -->
    <div class="col-12 col-lg-5">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-code-slash" style="color: var(--nd-navy-500);"></i>
                    <h5 class="nd-card-title mb-0">Dados Técnicos</h5>
                </div>
                <button class="btn btn-sm btn-light border" onclick="copyPayload()" title="Copiar JSON">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>
            <div class="nd-card-body p-0 position-relative">
                <textarea id="payloadText" class="d-none"><?= json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></textarea>
                <div class="p-3 bg-light font-monospace small text-dark" style="white-space: pre-wrap; word-break: break-all; min-height: 400px; max-height: 600px; overflow-y: auto; font-size: 0.8rem;"><?= htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></div>
            </div>
        </div>
    </div>
</div>

<script>
function copyPayload() {
    const text = document.getElementById('payloadText').value;
    navigator.clipboard.writeText(text).then(() => {
        alert('JSON copiado para a área de transferência!');
    });
}
</script>