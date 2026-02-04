<?php
/**
 * Dashboard de Métricas de Notificações
 * 
 * @var array $metrics
 * @var array $failuresByType
 * @var array $volumeByDay
 * @var float|null $avgProcessingTime
 * @var array $deadLetterQueue
 */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-speedometer2 text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Métricas do Worker</h1>
            <p class="text-muted mb-0 small">Monitoramento da fila de notificações e performance</p>
        </div>
    </div>
    <a href="/admin/notifications/outbox" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-list-ul me-1"></i> Ver Fila Completa
    </a>
</div>

<!-- Métricas Cards -->
<div class="row g-4 mb-4">
    <!-- Backlog -->
    <div class="col-md-6 col-lg-3">
        <div class="nd-card h-100">
            <div class="nd-card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="nd-avatar" style="background: var(--nd-warning-100); color: var(--nd-warning-600);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <span class="nd-badge nd-badge-warning">Aguardando</span>
                </div>
                <div class="h2 mb-1 fw-bold" style="color: var(--nd-navy-900);" id="metric-backlog">
                    <?= number_format($metrics['backlog'] ?? 0) ?>
                </div>
                <p class="text-muted small mb-0">Mensagens na fila</p>
            </div>
        </div>
    </div>

    <!-- Em Processamento -->
    <div class="col-md-6 col-lg-3">
        <div class="nd-card h-100">
            <div class="nd-card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="nd-avatar" style="background: var(--nd-info-100); color: var(--nd-info-600);">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <span class="nd-badge nd-badge-info">Enviando</span>
                </div>
                <div class="h2 mb-1 fw-bold" style="color: var(--nd-navy-900);" id="metric-sending">
                    <?= number_format($metrics['sending'] ?? 0) ?>
                </div>
                <p class="text-muted small mb-0">Em processamento agora</p>
            </div>
        </div>
    </div>

    <!-- Enviados Hoje -->
    <div class="col-md-6 col-lg-3">
        <div class="nd-card h-100">
            <div class="nd-card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="nd-avatar" style="background: var(--nd-success-100); color: var(--nd-success-600);">
                        <i class="bi bi-check-all"></i>
                    </div>
                    <span class="nd-badge nd-badge-success">Hoje</span>
                </div>
                <div class="h2 mb-1 fw-bold" style="color: var(--nd-navy-900);" id="metric-sent-today">
                    <?= number_format($metrics['sent_today'] ?? 0) ?>
                </div>
                <p class="text-muted small mb-0">Enviados com sucesso</p>
            </div>
        </div>
    </div>

    <!-- Falhas -->
    <div class="col-md-6 col-lg-3">
        <div class="nd-card h-100">
            <div class="nd-card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="nd-avatar" style="background: var(--nd-danger-100); color: var(--nd-danger-600);">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <span class="nd-badge nd-badge-danger">DLQ</span>
                </div>
                <div class="h2 mb-1 fw-bold" style="color: var(--nd-navy-900);" id="metric-failed">
                    <?= number_format($metrics['failed_total'] ?? 0) ?>
                </div>
                <p class="text-muted small mb-0">Total de falhas</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tempo Médio -->
    <div class="col-md-6 col-lg-4">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-clock-history" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Performance</h5>
            </div>
            <div class="nd-card-body">
                <div class="text-center py-3">
                    <?php if ($avgProcessingTime !== null): ?>
                        <div class="h1 mb-2 fw-bold" style="color: var(--nd-navy-900);">
                            <?= number_format($avgProcessingTime, 1) ?>s
                        </div>
                        <p class="text-muted mb-0">Tempo médio de envio (24h)</p>
                    <?php else: ?>
                        <div class="text-muted">
                            <i class="bi bi-dash-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Sem dados suficientes</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Falhas por Tipo -->
    <div class="col-md-6 col-lg-4">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart" style="color: var(--nd-danger-500);"></i>
                <h5 class="nd-card-title mb-0">Falhas por Tipo</h5>
            </div>
            <div class="nd-card-body">
                <?php if (empty($failuresByType)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-check-circle" style="font-size: 2rem; color: var(--nd-success-500);"></i>
                        <p class="mt-2 mb-0">Nenhuma falha registrada</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($failuresByType as $type => $count): ?>
                            <?php 
                            $typeLabel = match(strtolower($type)) {
                                'token_created' => 'Criação de Token',
                                'submission_received' => 'Protocolo Recebido',
                                'user_precreated' => 'Pré-cadastro',
                                'new_announcement' => 'Comunicado',
                                default => ucwords(str_replace('_', ' ', strtolower($type)))
                            };
                            ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-dark"><?= htmlspecialchars($typeLabel) ?></span>
                                <span class="nd-badge nd-badge-danger"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Volume Diário -->
    <div class="col-lg-4">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-graph-up" style="color: var(--nd-success-500);"></i>
                <h5 class="nd-card-title mb-0">Volume (7 dias)</h5>
            </div>
            <div class="nd-card-body">
                <?php if (empty($volumeByDay)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Sem dados</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-1">
                        <?php 
                        $maxVolume = max(array_map(fn($d) => $d['sent'] + $d['failed'], $volumeByDay)) ?: 1;
                        foreach ($volumeByDay as $day => $data): 
                            $total = $data['sent'] + $data['failed'];
                            $sentPercent = ($data['sent'] / max(1, $maxVolume)) * 100;
                            $failedPercent = ($data['failed'] / max(1, $maxVolume)) * 100;
                            $dayFormatted = date('d/m', strtotime($day));
                        ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted" style="width: 40px;"><?= $dayFormatted ?></span>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?= $sentPercent ?>%"></div>
                                        <div class="progress-bar bg-danger" style="width: <?= $failedPercent ?>%"></div>
                                    </div>
                                </div>
                                <span class="small fw-medium" style="width: 30px;"><?= $total ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Dead Letter Queue -->
<?php if (!empty($deadLetterQueue)): ?>
<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle" style="color: var(--nd-danger-500);"></i>
            <h5 class="nd-card-title mb-0">Dead Letter Queue (DLQ)</h5>
            <span class="nd-badge nd-badge-danger"><?= count($deadLetterQueue) ?></span>
        </div>
        <small class="text-muted">Mensagens que falharam após todas as tentativas</small>
    </div>
    <div class="nd-card-body p-0">
        <div class="table-responsive">
            <table class="nd-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tipo</th>
                        <th>Destinatário</th>
                        <th>Erro</th>
                        <th>Tentativas</th>
                        <th>Data</th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deadLetterQueue as $job): ?>
                        <?php
                        $typeLabel = match(strtolower($job['type'] ?? '')) {
                            'token_created' => 'Token',
                            'submission_received' => 'Protocolo',
                            'user_precreated' => 'Pré-cadastro',
                            default => ucwords(str_replace('_', ' ', strtolower($job['type'] ?? '')))
                        };
                        ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?= (int)$job['id'] ?></span></td>
                            <td><span class="small fw-medium"><?= htmlspecialchars($typeLabel) ?></span></td>
                            <td class="small"><?= htmlspecialchars($job['recipient_email'] ?? '') ?></td>
                            <td>
                                <span class="small text-danger text-truncate d-inline-block" style="max-width: 200px;" title="<?= htmlspecialchars($job['last_error'] ?? '') ?>">
                                    <?= htmlspecialchars(mb_substr($job['last_error'] ?? '-', 0, 50)) ?>...
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="nd-badge nd-badge-danger"><?= (int)$job['attempts'] ?>/<?= (int)($job['max_attempts'] ?? 5) ?></span>
                            </td>
                            <td class="small text-muted">
                                <?= date('d/m H:i', strtotime($job['created_at'] ?? 'now')) ?>
                            </td>
                            <td>
                                <a href="/admin/notifications/outbox/<?= (int)$job['id'] ?>" class="nd-btn nd-btn-ghost nd-btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Auto-refresh a cada 30 segundos
setInterval(function() {
    fetch('/admin/notifications/metrics/api')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('metric-backlog').textContent = data.data.backlog.toLocaleString();
                document.getElementById('metric-sending').textContent = data.data.sending.toLocaleString();
                document.getElementById('metric-sent-today').textContent = data.data.sent_today.toLocaleString();
                document.getElementById('metric-failed').textContent = data.data.failed_total.toLocaleString();
            }
        })
        .catch(() => {});
}, 30000);
</script>
