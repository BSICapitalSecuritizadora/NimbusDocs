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
            <p class="text-muted mb-0 small">Monitoramento e rastreabilidade de comunicados externos (Outbox)</p>
        </div>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success) ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="nd-alert nd-alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($error) ?></div>
    </div>
<?php endif; ?>

<!-- Filters Card -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros de Busca</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3">
            <div class="col-md-3">
                <label class="nd-label">Período (De)</label>
                <div class="nd-input-group">
                    <input type="date" name="from_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="nd-label">Período (Até)</label>
                <div class="nd-input-group">
                    <input type="date" name="to_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="nd-label">Situação</label>
                <select name="status" class="nd-input form-select">
                    <option value="">Todas</option>
                    <?php foreach ($statuses as $st): ?>
                        <?php
                        $stLabel = match($st) {
                            'PENDING' => 'Aguardando',
                            'SENDING' => 'Em Trânsito',
                            'SENT' => 'Concluído',
                            'FAILED' => 'Falha no Envio',
                            'CANCELLED' => 'Cancelado',
                            default => $st
                        };
                        ?>
                        <option value="<?= htmlspecialchars($st) ?>" <?= (($filters['status'] ?? '') === $st) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($stLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="nd-label">Finalidade</label>
                <select name="type" class="nd-input form-select">
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
    <div class="nd-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-list-ul" style="color: var(--nd-gold-500);"></i>
            <h5 class="nd-card-title mb-0">Registros Localizados</h5>
        </div>
        <small class="text-muted">Mostrando últimos 200 registros</small>
    </div>
    
    <div class="nd-card-body p-0">
        <?php if (!$rows): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum registro de envio encontrado.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Data/Hora</th>
                            <th>Situação/Finalidade</th>
                            <th>Destino/Conteúdo</th>
                            <th class="text-end">Ciclos</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $status = $r['status'] ?? '';
                            $badge = 'nd-badge-secondary';
                            $label = $status;
                            
                            switch ($status) {
                                case 'PENDING':
                                    $badge = 'bg-warning text-dark border-warning';
                                    $label = 'Aguardando';
                                    break;
                                case 'SENDING':
                                    $badge = 'bg-info text-white border-info';
                                    $label = 'Em Trânsito';
                                    break;
                                case 'SENT':
                                    $badge = 'nd-badge-success';
                                    $label = 'Concluído';
                                    break;
                                case 'FAILED':
                                    $badge = 'nd-badge-danger';
                                    $label = 'Falha';
                                    break;
                            }
                            
                            $typeKey = $r['type'] ?? '';
                            $typeLabel = match(strtolower($typeKey)) {
                                'token_created' => 'Criação de Token',
                                'password_reset' => 'Redefinição de Senha',
                                'welcome_email' => 'Boas-vindas',
                                'submission_received' => 'Protocolo Recebido',
                                'user_precreated' => 'Pré-cadastro de Usuário',
                                default => ucwords(str_replace(['_', '-'], ' ', strtolower($typeKey)))
                            };
                            ?>
                            <tr>
                                <td><span class="text-muted small">#<?= (int)$r['id'] ?></span></td>
                                <td>
                                    <div class="small text-dark">
                                        <i class="bi bi-calendar-event me-1 text-muted"></i>
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['created_at'] ?? 'now'))) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div><span class="nd-badge <?= $badge ?>"><?= htmlspecialchars($label) ?></span></div>
                                        <div><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($typeLabel) ?></span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-medium small mb-1">
                                            <i class="bi bi-envelope me-1 text-muted"></i>
                                            <?= htmlspecialchars($r['recipient_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <a href="/admin/notifications/outbox/<?= (int)$r['id'] ?>" class="text-decoration-none small text-truncate d-block" style="max-width: 300px; color: var(--nd-navy-600);">
                                            <?= htmlspecialchars($r['subject'] ?? '(Sem assunto)', ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <?php 
                                        $attempts = (int)($r['attempts'] ?? 0);
                                        $max = (int)($r['max_attempts'] ?? 5);
                                        $ratio = $attempts / max(1, $max);
                                        $color = $ratio > 0.8 ? 'text-danger' : 'text-muted';
                                    ?>
                                    <span class="small fw-medium <?= $color ?>"><?= $attempts ?> / <?= $max ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <?php if (($r['status'] ?? '') === 'FAILED'): ?>
                                            <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/reprocess" class="d-inline">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button class="nd-btn nd-btn-outline nd-btn-sm text-primary" title="Reprocessar">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (($r['status'] ?? '') === 'PENDING'): ?>
                                            <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/cancel" class="d-inline">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" title="Cancelar">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (in_array(($r['status'] ?? ''), ['FAILED', 'CANCELLED'], true) && Auth::hasRole('SUPER_ADMIN')): ?>
                                            <form method="post" action="/admin/notifications/outbox/<?= (int)$r['id'] ?>/reset" class="d-inline">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button class="nd-btn nd-btn-outline nd-btn-sm text-secondary border-start-0" title="Reiniciar (Super Admin)">
                                                    <i class="bi bi-bootstrap-reboot"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <a href="/admin/notifications/outbox/<?= (int)$r['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm border-start-0" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>