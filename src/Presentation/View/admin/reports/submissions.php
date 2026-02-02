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
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-file-earmark-bar-graph-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Relatório de Envios</h1>
            <p class="text-muted mb-0 small">Análise gerencial de solicitações recebidas via portal</p>
        </div>
    </div>
    <a href="/admin/reports/submissions/export?<?= $queryExport ?>" class="nd-btn nd-btn-outline nd-btn-sm hover-shadow-sm transition-fast">
        <i class="bi bi-cloud-download me-1"></i> Exportar CSV
    </a>
</div>

<!-- Filters -->
<div class="nd-card mb-4 border-0 shadow-sm">
    <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros de Pesquisa</h5>
    </div>
    <div class="nd-card-body p-4">
        <form class="row g-3">
             <div class="col-md-2">
                <label class="nd-label small text-muted text-uppercase fw-bold">Data Inicial</label>
                <div class="nd-input-group">
                    <input type="date" name="from_date" class="nd-input bg-light border-0"
                        value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="nd-label small text-muted text-uppercase fw-bold">Data Final</label>
                <div class="nd-input-group">
                     <input type="date" name="to_date" class="nd-input bg-light border-0"
                        value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="nd-label small text-muted text-uppercase fw-bold">Situação</label>
                <select name="status" class="nd-input form-select bg-light border-0">
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
                <label class="nd-label small text-muted text-uppercase fw-bold">E-mail do Solicitante</label>
                <div class="nd-input-group">
                    <input type="text" name="email" class="nd-input bg-light border-0"
                        placeholder="Ex: nome@empresa.com"
                        value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-envelope nd-input-icon text-muted"></i>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="nd-btn nd-btn-primary w-100 shadow-sm">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- KPIs -->
<div class="row g-4 mb-4">
    <!-- Total Recebido -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Total Recebido</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $kpis['total'] ?? 0 ?></div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-inbox-fill text-primary fs-5"></i>
                    </div>
                </div>
                <div class="mt-2 small text-muted d-flex align-items-center">
                    <i class="bi bi-calendar3 me-1"></i> No período selecionado
                </div>
            </div>
        </div>
    </div>

    <!-- Pendentes -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
           <div class="nd-card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Pendentes</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $kpis['pending'] ?? 0 ?></div>
                    </div>
                     <div class="rounded-circle bg-warning-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    </div>
                </div>
                 <div class="mt-2 small text-warning fw-medium d-flex align-items-center">
                    <i class="bi bi-clock me-1"></i> Aguardando análise
                </div>
            </div>
        </div>
    </div>

    <!-- Concluídas -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Concluídas</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $kpis['approved'] ?? 0 ?></div>
                    </div>
                     <div class="rounded-circle bg-success-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-check-lg text-success fs-5"></i>
                    </div>
                </div>
                 <div class="mt-2 small text-success fw-medium d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-1"></i> Finalizadas com sucesso
                </div>
            </div>
        </div>
    </div>

    <!-- Rejeitadas -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Rejeitadas</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $kpis['rejected'] ?? 0 ?></div>
                    </div>
                     <div class="rounded-circle bg-danger-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-x-lg text-danger fs-5"></i>
                    </div>
                </div>
                 <div class="mt-2 small text-danger fw-medium d-flex align-items-center">
                    <i class="bi bi-x-circle-fill me-1"></i> Solicitações negadas
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-12 col-xl-7">
        <div class="nd-card h-100 border-0 shadow-sm">
            <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bi bi-graph-up text-primary"></i>
                <h5 class="nd-card-title mb-0">Evolução Diária de Protocolos</h5>
            </div>
            <div class="nd-card-body p-4">
                <canvas id="chartSubmissionsByDay" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Ranking -->
    <div class="col-12 col-xl-5">
        <div class="nd-card h-100 border-0 shadow-sm">
            <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bi bi-trophy-fill text-warning"></i>
                <h5 class="nd-card-title mb-0">Solicitantes Mais Ativos (Top 5)</h5>
            </div>
            <div class="nd-card-body p-0">
                <?php if (!$ranking): ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5">
                       <i class="bi bi-bar-chart-steps text-muted display-4 mb-2 opacity-25"></i>
                        <p class="text-muted mb-0 small">Sem dados de ranking no período.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($ranking as $index => $row): ?>
                            <div class="list-group-item border-light px-4 py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="fw-bold text-muted small" style="width: 20px;">#<?= $index + 1 ?></div>
                                        <div class="nd-avatar nd-avatar-sm d-none d-sm-flex" style="background-color: var(--nd-gray-100); color: var(--nd-gray-600);">
                                            <?= strtoupper(substr($row['full_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-dark fw-bold small mb-0">
                                                <?= htmlspecialchars($row['full_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-muted x-small">
                                                <?= htmlspecialchars($row['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark border rounded-pill px-3">
                                            <?= (int)$row['total'] ?> envios
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed List -->
<div class="nd-card border-0 shadow-sm">
    <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
        <i class="bi bi-table text-dark"></i>
        <h5 class="nd-card-title mb-0">Detalhamento dos Envios</h5>
    </div>
    <div class="nd-card-body p-0">
        <?php if (!$submissions): ?>
            <div class="text-center py-5">
                 <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 64px; height: 64px;">
                        <i class="bi bi-inbox fs-2"></i>
                    </div>
                </div>
                <h6 class="fw-bold text-dark">Nenhum resultado encontrado</h6>
                <p class="text-muted mb-0 small">Tente ajustar os filtros de pesquisa para visualizar os dados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase text-muted x-small fw-bold">Solicitante</th>
                            <th class="text-uppercase text-muted x-small fw-bold">Assunto/Protocolo</th>
                            <th class="text-uppercase text-muted x-small fw-bold">Situação Atual</th>
                            <th class="text-uppercase text-muted x-small fw-bold">Data Criação</th>
                            <th class="pe-4 text-uppercase text-muted x-small fw-bold text-end">Última Atualização</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td class="ps-4">
                                     <div class="d-flex align-items-center gap-2">
                                        <div class="nd-avatar nd-avatar-xs rounded-circle bg-light border d-none d-sm-flex">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                        <div>
                                            <span class="d-block text-dark fw-bold small">
                                                <?= htmlspecialchars($s['user_name'] ?? 'Anônimo', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="d-block text-muted x-small">
                                                <?= htmlspecialchars($s['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small fw-medium">
                                        <?= htmlspecialchars($s['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?php if(!empty($s['protocol'])): ?>
                                        <div class="x-small font-monospace text-muted mt-1">#<?= $s['protocol'] ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $st = strtoupper($s['status'] ?? '');
                                    $badge = 'nd-badge-secondary';
                                    $label = $st ?: 'Desconhecido';
                                    $icon = 'bi-question-circle';
                                    
                                    if ($st === 'PENDING') {
                                        $badge = 'nd-badge-warning';
                                        $label = 'Pendente';
                                        $icon = 'bi-hourglass-split';
                                    } elseif ($st === 'COMPLETED' || $st === 'FINALIZADA') { // Suporte a legado se houver
                                        $badge = 'nd-badge-success';
                                        $label = 'Concluída';
                                        $icon = 'bi-check-all';
                                    } elseif ($st === 'REJECTED') {
                                        $badge = 'nd-badge-danger';
                                        $label = 'Rejeitada';
                                        $icon = 'bi-x-circle';
                                    } elseif ($st === 'UNDER_REVIEW') {
                                        $badge = 'nd-badge-info';
                                        $label = 'Em Análise';
                                        $icon = 'bi-search';
                                    }
                                    ?>
                                    <span class="nd-badge <?= $badge ?>">
                                        <i class="bi <?= $icon ?> me-1"></i> <?= htmlspecialchars($label) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($s['created_at'] ?? 'now'))) ?>
                                        <small class="d-block text-opacity-75"><?= htmlspecialchars(date('H:i', strtotime($s['created_at'] ?? 'now'))) ?></small>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                     <div class="small text-muted">
                                        <?= $s['updated_at'] ? date('d/m/Y', strtotime($s['updated_at'])) : '-' ?>
                                        <?php if($s['updated_at']): ?>
                                            <small class="d-block text-opacity-75"><?= date('H:i', strtotime($s['updated_at'])) ?></small>
                                        <?php endif; ?>
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
                    backgroundColor: (context) => {
                        const bg = context.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        bg.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
                        bg.addColorStop(1, 'rgba(13, 110, 253, 0)');
                        return bg;
                    },
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1e293b',
                        bodyColor: '#475569',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [4, 4],
                            color: '#f1f5f9'
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b'
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b'
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }
</script>

<style>
    @media (max-width: 575.98px) {
        .nd-page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .nd-page-header > .d-flex {
            width: 100%;
        }
        .nd-page-header .nd-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>