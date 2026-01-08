<?php

/** @var array $filters */
/** @var array $kpis */
/** @var array $chartLabels */
/** @var array $chartValues */
/** @var array $ranking */
/** @var array $submissions */

// montar query para export
$queryExport = http_build_query([
    'status'    => $filters['status']   ?? '',
    'email'     => $filters['email']    ?? '',
    'from_date' => $filters['from_date'] ?? '',
    'to_date'   => $filters['to_date']  ?? '',
]);
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-file-earmark-bar-graph-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Relatório de Envios</h1>
            <p class="text-muted mb-0 small">Análise gerencial de solicitações recebidas via portal</p>
        </div>
    </div>
    <a href="/admin/reports/submissions/export?<?= $queryExport ?>" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-download me-1"></i> Exportar Dados (CSV)
    </a>
</div>

<!-- Filters -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros de Pesquisa</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3">
            <div class="col-md-2">
                <label class="nd-label">Data Inicial</label>
                <div class="nd-input-group">
                    <input type="date" name="from_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="nd-label">Data Final</label>
                <div class="nd-input-group">
                    <input type="date" name="to_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="nd-label">Situação</label>
                <select name="status" class="nd-input form-select">
                    <?php
                    $statusOptions = [
                        ''           => 'Todas',
                        'PENDENTE'   => 'Pendente',
                        'FINALIZADA' => 'Concluída',
                        'REJEITADA'  => 'Rejeitada',
                    ];
                    foreach ($statusOptions as $value => $label):
                        $selected = ($filters['status'] ?? '') === $value ? 'selected' : '';
                    ?>
                        <option value="<?= $value ?>" <?= $selected ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="nd-label">E-mail do Solicitante</label>
                <div class="nd-input-group">
                    <input type="text" name="email" class="nd-input"
                        placeholder="Ex: contato@empresa.com"
                        value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-envelope nd-input-icon"></i>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="nd-btn nd-btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Filtrar Relatório
                </button>
            </div>
        </form>
    </div>
</div>

<!-- KPIs -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="nd-card h-100 border-start border-4 border-primary">
            <div class="nd-card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="small text-muted text-uppercase fw-bold">Total Recebido</div>
                    <div class="nd-avatar nd-avatar-sm bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark"><?= $kpis['total'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="nd-card h-100 border-start border-4 border-warning">
            <div class="nd-card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="small text-muted text-uppercase fw-bold">Pendentes</div>
                    <div class="nd-avatar nd-avatar-sm bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark"><?= $kpis['pending'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="nd-card h-100 border-start border-4 border-success">
            <div class="nd-card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="small text-muted text-uppercase fw-bold">Concluídas</div>
                    <div class="nd-avatar nd-avatar-sm bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark"><?= $kpis['approved'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="nd-card h-100 border-start border-4 border-danger">
            <div class="nd-card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="small text-muted text-uppercase fw-bold">Rejeitadas</div>
                    <div class="nd-avatar nd-avatar-sm bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark"><?= $kpis['rejected'] ?? 0 ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-12 col-xl-7">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-graph-up" style="color: var(--nd-blue-500);"></i>
                <h5 class="nd-card-title mb-0">Evolução Diária de Protocolos</h5>
            </div>
            <div class="nd-card-body">
                <canvas id="chartSubmissionsByDay" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Ranking -->
    <div class="col-12 col-xl-5">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-trophy" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Solicitantes Mais Ativos (Top 5)</h5>
            </div>
            <div class="nd-card-body p-0">
                <?php if (!$ranking): ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-0 small">Nenhum dado para exibir no período.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="nd-table">
                            <thead>
                                <tr>
                                    <th>Solicitante</th>
                                    <th class="text-end">Volume</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranking as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-medium small">
                                                    <?= htmlspecialchars($row['full_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span class="text-muted x-small">
                                                    <?= htmlspecialchars($row['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?= (int)$row['total'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed List -->
<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-table" style="color: var(--nd-navy-500);"></i>
        <h5 class="nd-card-title mb-0">Detalhamento dos Envios</h5>
    </div>
    <div class="nd-card-body p-0">
        <?php if (!$submissions): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum protocolo encontrado com os filtros aplicados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Assunto</th>
                            <th>Situação</th>
                            <th>Criação</th>
                            <th>Última Atualização</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-medium small">
                                            <?= htmlspecialchars($s['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="text-muted x-small">
                                            <?= htmlspecialchars($s['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small">
                                        <?= htmlspecialchars($s['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $st = $s['status'] ?? '';
                                    $badge = 'nd-badge-secondary';
                                    $label = 'Desconhecido';
                                    
                                    if ($st === 'PENDENTE') {
                                        $badge = 'nd-badge-warning'; // ou bg-warning manual se preferir
                                        $badge = 'bg-warning text-dark border-warning';
                                        $label = 'Pendente';
                                    } elseif ($st === 'FINALIZADA') {
                                        $badge = 'nd-badge-success';
                                        $label = 'Concluída';
                                    } elseif ($st === 'REJEITADA') {
                                        $badge = 'nd-badge-danger';
                                        $label = 'Rejeitada';
                                    }
                                    ?>
                                    <span class="nd-badge <?= $badge ?>">
                                        <?= htmlspecialchars($label) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($s['created_at'] ?? 'now'))) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <?= $s['updated_at'] ? date('d/m/Y H:i', strtotime($s['updated_at'])) : '-' ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>;
    const values = <?= json_encode($chartValues) ?>;

    const ctx = document.getElementById('chartSubmissionsByDay');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Submissões',
                    data: values,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#e9ecef'
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
</script>