<?php

/** @var int $totalSubmissions */
/** @var int $pendingSubmissions */
/** @var int $approvedSubmissions */
/** @var int $rejectedSubmissions */
/** @var int $totalPortalUsers */
/** @var int $publishedDocuments */
/** @var array $recentSubmissions */
/** @var array $recentLogs */
?>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-1 fw-semibold" style="color: var(--nd-gray-900);">Dashboard</h1>
            <p class="text-muted mb-0" style="font-size: 0.875rem;">Visão geral do sistema</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/reports/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
                <i class="bi bi-download"></i>
                Exportar
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Submissions -->
        <div class="col-6 col-lg-3">
            <div class="nd-metric-card primary">
                <div class="nd-metric-card-gradient"></div>
                <div class="nd-metric-icon">
                    <i class="bi bi-inbox-fill"></i>
                </div>
                <div class="nd-metric-value"><?= number_format($totalSubmissions) ?></div>
                <div class="nd-metric-label">Total de Submissões</div>
            </div>
        </div>
        
        <!-- Pending -->
        <div class="col-6 col-lg-3">
            <div class="nd-metric-card warning">
                <div class="nd-metric-card-gradient"></div>
                <div class="nd-metric-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="nd-metric-value"><?= number_format($pendingSubmissions) ?></div>
                <div class="nd-metric-label">Pendentes</div>
            </div>
        </div>
        
        <!-- Approved -->
        <div class="col-6 col-lg-3">
            <div class="nd-metric-card success">
                <div class="nd-metric-card-gradient"></div>
                <div class="nd-metric-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="nd-metric-value"><?= number_format($approvedSubmissions) ?></div>
                <div class="nd-metric-label">Aprovadas</div>
            </div>
        </div>
        
        <!-- Rejected -->
        <div class="col-6 col-lg-3">
            <div class="nd-metric-card danger">
                <div class="nd-metric-card-gradient"></div>
                <div class="nd-metric-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="nd-metric-value"><?= number_format($rejectedSubmissions) ?></div>
                <div class="nd-metric-label">Rejeitadas</div>
            </div>
        </div>
    </div>
    
    <!-- Second Row of Metrics -->
    <div class="row g-4 mb-4">
        <!-- Portal Users -->
        <div class="col-6 col-lg-3">
            <div class="nd-metric-card gold">
                <div class="nd-metric-card-gradient"></div>
                <div class="nd-metric-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="nd-metric-value"><?= number_format($totalPortalUsers) ?></div>
                <div class="nd-metric-label">Usuários do Portal</div>
            </div>
        </div>
        
        <!-- Published Documents -->
        <div class="col-6 col-lg-3">
            <div class="nd-metric-card info">
                <div class="nd-metric-card-gradient"></div>
                <div class="nd-metric-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div class="nd-metric-value"><?= number_format($publishedDocuments) ?></div>
                <div class="nd-metric-label">Documentos Publicados</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Recent Submissions -->
        <div class="col-lg-8">
            <div class="nd-card">
                <div class="nd-card-header d-flex align-items-center justify-content-between">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-inbox me-2" style="color: var(--nd-gold-500);"></i>
                        Últimas Submissões
                    </h5>
                    <a href="/admin/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
                        Ver tudo
                    </a>
                </div>
                <div class="nd-card-body p-0">
                    <?php if (!$recentSubmissions): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: var(--nd-gray-300);"></i>
                            <p class="text-muted mt-2 mb-0">Nenhuma submissão recente.</p>
                        </div>
                    <?php else: ?>
                        <table class="nd-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentSubmissions as $s): ?>
                                    <?php
                                    $statusClass = match($s['status'] ?? '') {
                                        'PENDING', 'UNDER_REVIEW' => 'warning',
                                        'COMPLETED', 'APPROVED' => 'success',
                                        'REJECTED' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusLabel = match($s['status'] ?? '') {
                                        'PENDING' => 'Pendente',
                                        'UNDER_REVIEW' => 'Em Análise',
                                        'COMPLETED' => 'Concluída',
                                        'APPROVED' => 'Aprovada',
                                        'REJECTED' => 'Rejeitada',
                                        default => $s['status'] ?? '-'
                                    };
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold" style="color: var(--nd-gray-800);">#<?= $s['id'] ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($s['user_name'] ?? 'Usuário', ENT_QUOTES) ?></td>
                                        <td>
                                            <span style="color: var(--nd-gray-500);">
                                                <?= htmlspecialchars($s['submitted_at'] ?? '', ENT_QUOTES) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="nd-badge nd-badge-<?= $statusClass ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="/admin/submissions/<?= $s['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar with Activity & Alerts -->
        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="nd-card mb-4">
                <div class="nd-card-header">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-activity me-2" style="color: var(--nd-gold-500);"></i>
                        Atividade Recente
                    </h5>
                </div>
                <div class="nd-card-body">
                    <?php if (!$recentLogs): ?>
                        <p class="text-muted mb-0 small">Nenhuma atividade registrada.</p>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach (array_slice($recentLogs, 0, 5) as $log): ?>
                                <div class="d-flex gap-3">
                                    <div class="nd-avatar" style="width: 36px; height: 36px; font-size: 0.875rem; flex-shrink: 0;">
                                        <i class="bi bi-lightning-fill" style="color: var(--nd-gold-500); font-size: 0.875rem;"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-semibold small" style="color: var(--nd-gray-800);">
                                            <?= htmlspecialchars($log['action'] ?? '', ENT_QUOTES) ?>
                                        </div>
                                        <div class="text-muted small text-truncate">
                                            <?= htmlspecialchars($log['summary'] ?? '', ENT_QUOTES) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars($log['occurred_at'] ?? '', ENT_QUOTES) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Alerts -->
            <div class="nd-card">
                <div class="nd-card-header">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2" style="color: var(--nd-warning);"></i>
                        Alertas
                    </h5>
                </div>
                <div class="nd-card-body">
                    <?php $a = $alerts ?? []; ?>
                    <div class="d-flex flex-column gap-3">
                        <div class="nd-alert-card warning">
                            <div class="nd-alert-card-title">
                                <i class="bi bi-clock me-1"></i>
                                Submissões > 7 dias
                            </div>
                            <div class="nd-alert-card-text"><?= (int)($a['oldPending'] ?? 0) ?> pendentes/em análise</div>
                        </div>
                        
                        <div class="nd-alert-card danger">
                            <div class="nd-alert-card-title">
                                <i class="bi bi-key me-1"></i>
                                Tokens expirados
                            </div>
                            <div class="nd-alert-card-text"><?= (int)($a['expiredTokens'] ?? 0) ?> encontrados</div>
                        </div>
                        
                        <div class="nd-alert-card warning">
                            <div class="nd-alert-card-title">
                                <i class="bi bi-person-x me-1"></i>
                                Usuários inativos
                            </div>
                            <div class="nd-alert-card-text"><?= (int)($a['inactiveUsers30'] ?? 0) ?> há +30 dias</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="nd-card">
                <div class="nd-card-header">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-pie-chart me-2" style="color: var(--nd-gold-500);"></i>
                        Submissões por Status
                    </h5>
                </div>
                <div class="nd-card-body">
                    <canvas id="chartStatus" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="nd-card">
                <div class="nd-card-header">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-graph-up me-2" style="color: var(--nd-gold-500);"></i>
                        Submissões (30 dias)
                    </h5>
                </div>
                <div class="nd-card-body">
                    <canvas id="chartDaily" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    const statusData = <?= json_encode($chartStatusCounts ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
    const dailyData  = <?= json_encode($chartDailyCounts ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

    // Chart 1 - Status (Doughnut)
    const statusLabels = ['Aprovado', 'Rejeitado', 'Pendente', 'Em análise'];
    const statusKeys   = ['APPROVED', 'REJECTED', 'PENDING', 'IN_REVIEW'];
    const statusValues = statusKeys.map(k => statusData[k] ?? 0);
    
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Chart 2 - Daily (Line)
    const dailyLabels = (dailyData||[]).map(r => r.date);
    const dailyValues = (dailyData||[]).map(r => r.total);
    
    new Chart(document.getElementById('chartDaily'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Submissões',
                data: dailyValues,
                borderColor: '#d4a84b',
                backgroundColor: 'rgba(212, 168, 75, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#d4a84b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 11
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
})();
</script>