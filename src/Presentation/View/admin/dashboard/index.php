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
            <h1 class="h4 mb-1 fw-semibold" style="color: var(--nd-gray-900);">Visão Geral</h1>
            <p class="text-muted mb-0" style="font-size: 0.875rem;">Monitoramento de indicadores e performance do sistema</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/reports/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
                <i class="bi bi-download"></i>
                Exportar Relatório
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
                <div class="nd-metric-label">Envios Recebidos</div>
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
                <div class="nd-metric-label">Aguardando Análise</div>
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
                <div class="nd-metric-label">Aprovados</div>
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
                <div class="nd-metric-label">Rejeitados</div>
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
                <div class="nd-metric-label">Usuários Cadastrados</div>
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
                <div class="nd-metric-label">Documentos Vigentes</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Recent Submissions -->
        <div class="col-lg-8">
            <div class="nd-card h-100">
                <div class="nd-card-header d-flex align-items-center justify-content-between">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-inbox me-2" style="color: var(--nd-gold-500);"></i>
                        Envios Recentes
                    </h5>
                    <a href="/admin/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
                        Ver Todas
                    </a>
                </div>
                <div class="nd-card-body p-0">
                    <?php if (!$recentSubmissions): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: var(--nd-gray-300);"></i>
                            <p class="text-muted mt-2 mb-0">Nenhuma submissão recente encontrada.</p>
                        </div>
                    <?php else: ?>
                        <table class="nd-table">
                            <thead>
                                <tr>
                                    <th>Solicitante</th>
                                    <th>Data de Envio</th>
                                    <th>Situação</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="align-middle">
                                <?php foreach ($recentSubmissions as $s): ?>
                                    <?php
                                    $statusConfig = match($s['status'] ?? '') {
                                        'PENDING'       => ['label' => 'Pendente', 'class' => 'warning', 'icon' => 'bi-clock'],
                                        'UNDER_REVIEW'  => ['label' => 'Em Análise', 'class' => 'info', 'icon' => 'bi-search'],
                                        'APPROVED'      => ['label' => 'Aprovada', 'class' => 'success', 'icon' => 'bi-check-circle'],
                                        'COMPLETED'     => ['label' => 'Concluída', 'class' => 'success', 'icon' => 'bi-check-all'],
                                        'REJECTED'      => ['label' => 'Rejeitada', 'class' => 'danger', 'icon' => 'bi-x-circle'],
                                        default         => ['label' => $s['status'] ?? '-', 'class' => 'secondary', 'icon' => 'bi-dash']
                                    };
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="nd-avatar nd-avatar-xs text-white" style="background-color: var(--nd-navy-500); width: 24px; height: 24px; font-size: 0.75rem;">
                                                    <?= strtoupper(substr($s['user_name'] ?? 'U', 0, 1)) ?>
                                                </div>
                                                <span class="text-dark fw-medium"><?= htmlspecialchars($s['user_name'] ?? 'Usuário', ENT_QUOTES) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($s['submitted_at'] ?? 'now')) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill fw-normal bg-<?= $statusConfig['class'] ?>-subtle text-<?= $statusConfig['class'] ?>-emphasis border border-<?= $statusConfig['class'] ?>-subtle">
                                                <i class="bi <?= $statusConfig['icon'] ?> me-1"></i>
                                                <?= $statusConfig['label'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="/admin/submissions/<?= $s['id'] ?>" class="nd-btn nd-btn-ghost nd-btn-sm" title="Ver detalhes">
                                                <i class="bi bi-chevron-right"></i>
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
                <div class="nd-card-header d-flex align-items-center justify-content-between">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-activity me-2" style="color: var(--nd-gold-500);"></i>
                        Últimas Atividades
                    </h5>
                    <a href="/admin/audit-logs" class="nd-btn nd-btn-link nd-btn-sm p-0 text-decoration-none" style="font-size: 0.8rem;">Histórico Completo</a>
                </div>
                <div class="nd-card-body p-0">
                    <?php if (!$recentLogs): ?>
                        <div class="p-3 text-center text-muted small">Nenhuma atividade recente registrada.</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach (array_slice($recentLogs, 0, 6) as $log): ?>
                                <?php
                                    $actionKey = $log['action'] ?? 'UNKNOWN';
                                    
                                    // Map text
                                    $actionText = match($actionKey) {
                                        'LOGIN_SUCCESS' => 'Acesso realizado',
                                        'LOGIN_FAILED' => 'Tentativa de acesso falhou',
                                        'PORTAL_USER_CREATED' => 'Usuário cadastrado',
                                        'PORTAL_USER_UPDATED' => 'Dados de usuário atualizados',
                                        'PORTAL_ACCESS_LINK_GENERATED' => 'Link de acesso gerado',
                                        'SUBMISSION_CREATED' => 'Nova submissão recebida',
                                        'PORTAL_SUBMISSION_CREATED' => 'Submissão via Portal',
                                        default => ucwords(strtolower(str_replace('_', ' ', $actionKey)))
                                    };

                                    // Map color/icon
                                    $iconClass = 'bi-circle-fill';
                                    $colorStyle = 'text-secondary';
                                    $bgStyle = 'bg-light';
                                    
                                    if (str_contains($actionKey, 'LOGIN_SUCCESS')) {
                                        $iconClass = 'bi-check-circle-fill';
                                        $colorStyle = 'text-success';
                                        $bgStyle = 'bg-success-subtle';
                                    } elseif (str_contains($actionKey, 'FAILED')) {
                                        $iconClass = 'bi-x-circle-fill';
                                        $colorStyle = 'text-danger';
                                        $bgStyle = 'bg-danger-subtle';
                                    } elseif (str_contains($actionKey, 'CREATED')) {
                                        $iconClass = 'bi-plus-circle-fill';
                                        $colorStyle = 'text-primary';
                                        $bgStyle = 'bg-primary-subtle';
                                    } elseif (str_contains($actionKey, 'UPDATED')) {
                                        $iconClass = 'bi-pencil-fill';
                                        $colorStyle = 'text-info';
                                        $bgStyle = 'bg-info-subtle';
                                    }
                                ?>
                                <div class="list-group-item d-flex gap-3 align-items-start py-3 border-light">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 <?= $bgStyle ?>" 
                                         style="width: 32px; height: 32px;">
                                        <i class="bi <?= $iconClass ?> <?= $colorStyle ?>" style="font-size: 0.875rem;"></i>
                                    </div>
                                    <div style="min-width: 0;" class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="fw-medium small text-dark"><?= $actionText ?></span>
                                            <small class="text-muted" style="font-size: 0.7rem; white-space: nowrap;"><?= date('H:i', strtotime($log['occurred_at'])) ?></small>
                                        </div>
                                        <div class="text-muted small text-truncate" title="<?= htmlspecialchars($log['summary'] ?? '', ENT_QUOTES) ?>">
                                            <?= htmlspecialchars($log['summary'] ?? '', ENT_QUOTES) ?>
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
                        <i class="bi bi-bell me-2" style="color: var(--nd-warning);"></i>
                        Atenção Necessária
                    </h5>
                </div>
                <div class="nd-card-body">
                    <?php $a = $alerts ?? []; ?>
                    <?php if(empty($a['oldPending']) && empty($a['expiredTokens']) && empty($a['inactiveUsers30'])): ?>
                        <div class="text-center text-muted small">Nenhum alerta pendente no momento.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php if (!empty($a['oldPending'])): ?>
                                <div class="alert alert-warning d-flex align-items-center p-2 mb-0 border-0 bg-warning-subtle text-warning-emphasis small">
                                    <i class="bi bi-clock-history me-2 fs-6"></i>
                                    <div>
                                        <strong><?= (int)$a['oldPending'] ?> envios</strong> aguardando análise há > 7 dias.
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($a['expiredTokens'])): ?>
                                <div class="alert alert-danger d-flex align-items-center p-2 mb-0 border-0 bg-danger-subtle text-danger-emphasis small">
                                    <i class="bi bi-shield-x me-2 fs-6"></i>
                                    <div>
                                        <strong><?= (int)$a['expiredTokens'] ?> tokens</strong> de acesso expirados.
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($a['inactiveUsers30'])): ?>
                                <div class="alert alert-secondary d-flex align-items-center p-2 mb-0 border-0 bg-secondary-subtle text-secondary-emphasis small">
                                    <i class="bi bi-person-dash me-2 fs-6"></i>
                                    <div>
                                        <strong><?= (int)$a['inactiveUsers30'] ?> usuários</strong> sem acesso há > 30 dias.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
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
                        Distribuição por Situação
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
                        Volume de Envios (30 Dias)
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
    const statusLabels = ['Aprovada', 'Rejeitada', 'Pendente', 'Em Análise', 'Concluída'];
    const statusKeys   = ['APPROVED', 'REJECTED', 'PENDING', 'UNDER_REVIEW', 'COMPLETED'];
    const statusValues = statusKeys.map(k => statusData[k] ?? 0);
    
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#14b8a6'],
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