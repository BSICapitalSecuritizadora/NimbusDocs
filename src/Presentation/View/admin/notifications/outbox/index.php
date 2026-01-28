<?php
use App\Support\Auth;

/** @var array $filters */
/** @var array $rows */
/** @var array $types */
/** @var array $statuses */
/** @var string $csrfToken */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-envelope-paper-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Auditoria de Envios</h1>
            <p class="text-muted mb-0 small">Monitoramento da fila de notificações externas (Outbox)</p>
        </div>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-4" id="alertSuccess">
        <i class="bi bi-check-circle-fill text-success"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success) ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="nd-alert nd-alert-danger mb-4" id="alertError">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($error) ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<!-- Filters Card -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel text-primary"></i>
        <h5 class="nd-card-title mb-0">Filtros de Auditoria</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3">
            <div class="col-md-3">
                <label class="nd-label">Período (De)</label>
                <div class="nd-input-group">
                    <input type="date" name="from_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-calendar-event nd-input-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <label class="nd-label">Período (Até)</label>
                <div class="nd-input-group">
                    <input type="date" name="to_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-calendar-range nd-input-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <label class="nd-label">Situação</label>
                <div class="nd-input-group">
                    <select name="status" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todas</option>
                        <?php foreach ($statuses as $st): ?>
                            <?php
                            $stLabel = match($st) {
                                'PENDING' => 'Aguardando Envio',
                                'SENDING' => 'Em Processamento',
                                'SENT' => 'Entregue com Sucesso',
                                'FAILED' => 'Falha na Entrega',
                                'CANCELLED' => 'Cancelado Manualmente',
                                default => $st
                            };
                            ?>
                            <option value="<?= htmlspecialchars($st) ?>" <?= (($filters['status'] ?? '') === $st) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($stLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="bi bi-activity nd-input-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <label class="nd-label">Finalidade</label>
                <div class="nd-input-group">
                    <select name="type" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todas</option>
                        <?php foreach ($types as $tp): ?>
                            <?php
                            $tpLabel = match(strtolower($tp)) {
                                'token_created' => 'Criação de Token',
                                'password_reset' => 'Redefinição de Senha',
                                'welcome_email' => 'Boas-vindas',
                                'submission_received' => 'Protocolo Recebido',
                                'user_precreated' => 'Pré-cadastro de Usuário',
                                default => ucwords(str_replace(['_', '-'], ' ', strtolower($tp)))
                            };
                            ?>
                            <option value="<?= htmlspecialchars($tp) ?>" <?= (($filters['type'] ?? '') === $tp) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tpLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="bi bi-tag nd-input-icon"></i>
                </div>
            </div>
            
            <div class="col-md-9">
                <label class="nd-label">Destinatário (E-mail)</label>
                <div class="nd-input-group">
                    <input type="text" name="email" class="nd-input"
                        placeholder="Ex: usuario@empresa.com"
                        value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-envelope nd-input-icon"></i>
                </div>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="nd-btn nd-btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Localizar Registros
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results Card -->
<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center justify-content-between p-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-list-check" style="color: var(--nd-gold-500);"></i>
            <h5 class="nd-card-title mb-0">Logs de Envio</h5>
        </div>
        <small class="text-muted"><i class="bi bi-clock-history me-1"></i> Mostrando últimos 200 eventos</small>
    </div>
    
    <div class="nd-card-body p-0">
        <?php if (!$rows): ?>
            <div class="text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-inbox text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhum registro encontrado</p>
                <p class="text-muted small mb-0">Tente ajustar os filtros acima.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Audit ID</th>
                            <th>Data do Evento</th>
                            <th>Classificação</th>
                            <th>Destino e Conteúdo</th>
                            <th class="text-center">Tentativas</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $status = $r['status'] ?? '';
                            $badge = 'nd-badge-secondary';
                            $label = $status;
                            $icon = 'bi-circle';
                            
                            switch ($status) {
                                case 'PENDING':
                                    $badge = 'nd-badge-warning';
                                    $label = 'Aguardando';
                                    $icon = 'bi-hourglass-split';
                                    break;
                                case 'SENDING':
                                    $badge = 'bg-info text-white';
                                    $label = 'Enviando';
                                    $icon = 'bi-arrow-right-circle';
                                    break;
                                case 'SENT':
                                    $badge = 'nd-badge-success';
                                    $label = 'Concluído';
                                    $icon = 'bi-check-all';
                                    break;
                                case 'FAILED':
                                    $badge = 'nd-badge-danger';
                                    $label = 'Falhou';
                                    $icon = 'bi-x-circle';
                                    break;
                                case 'CANCELLED':
                                    $badge = 'bg-secondary text-white';
                                    $label = 'Cancelado';
                                    $icon = 'bi-dash-circle';
                                    break;
                            }
                            
                            $typeKey = $r['type'] ?? '';
                            $typeLabel = match(strtolower($typeKey)) {
                                'token_created' => 'Criação de Token',
                                'password_reset' => 'Redef. de Senha',
                                'welcome_email' => 'Boas-vindas',
                                'submission_received' => 'Protocolo',
                                'user_precreated' => 'Pré-cadastro',
                                default => ucwords(str_replace(['_', '-'], ' ', strtolower($typeKey)))
                            };
                            ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">#<?= (int)$r['id'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-dark small">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($r['created_at'] ?? 'now'))) ?>
                                        <span class="text-muted"><?= htmlspecialchars(date('H:i', strtotime($r['created_at'] ?? 'now'))) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <span class="nd-badge <?= $badge ?>">
                                            <i class="bi <?= $icon ?> me-1"></i> <?= htmlspecialchars($label) ?>
                                        </span>
                                        <small class="text-muted fw-bold" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars(strtoupper($typeLabel)) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column" style="max-width: 350px;">
                                        <span class="text-dark fw-medium small mb-1 text-truncate">
                                            <i class="bi bi-envelope-at me-1 text-primary"></i>
                                            <?= htmlspecialchars($r['recipient_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <a href="/admin/notifications/outbox/<?= (int)$r['id'] ?>" class="text-decoration-none text-muted small text-truncate hover-primary">
                                            <?= htmlspecialchars($r['subject'] ?? '(Sem assunto)', ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $attempts = (int)($r['attempts'] ?? 0);
                                        $max = (int)($r['max_attempts'] ?? 5);
                                        $percent = ($attempts / max(1, $max)) * 100;
                                        $barColor = $attempts >= $max ? 'bg-danger' : 'bg-primary';
                                    ?>
                                    <div class="d-flex flex-column align-items-center" style="width: 60px; margin: 0 auto;">
                                        <div class="small fw-medium mb-1"><?= $attempts ?> / <?= $max ?></div>
                                        <div class="progress w-100" style="height: 4px;">
                                            <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <?php if (($r['status'] ?? '') === 'FAILED'): ?>
                                            <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/reprocess" class="d-inline" onsubmit="return confirm('Reenviar esta mensagem?');">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button class="nd-btn nd-btn-outline nd-btn-sm text-primary" title="Reprocessar Envio">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (($r['status'] ?? '') === 'PENDING'): ?>
                                            <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/cancel" class="d-inline" onsubmit="return confirm('Cancelar envio?');">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" title="Cancelar Envio">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <a href="/admin/notifications/outbox/<?= (int)$r['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm border-start-0" title="Ver Detalhes do Log">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
             <div class="nd-card-footer p-3 border-top text-end text-muted small">
                Todos os horários estão em <strong>Brasília/DF</strong>
            </div>
        <?php endif; ?>
    </div>
</div>