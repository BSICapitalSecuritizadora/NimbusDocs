<?php
/**
 * Admin Dashboard v2.0 - Premium Financial Design
 * 
 * @var int $totalSubmissions
 * @var int $pendingSubmissions
 * @var int $approvedSubmissions
 * @var int $rejectedSubmissions
 * @var int $totalPortalUsers
 * @var int $publishedDocuments
 * @var array $recentSubmissions
 * @var array $recentLogs
 * @var array $chartStatusCounts
 * @var array $chartDailyCounts
 * @var array $alerts
 */
?>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="nd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="nd-page-title">Visão Geral</h1>
            <p class="nd-page-subtitle">Monitoramento de indicadores e performance do sistema</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/reports/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
                <i class="bi bi-download"></i>
                Exportar Relatório
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Total Submissions -->
        <div class="col-6 col-lg-4">
            <div class="nd-metric-card primary">
                <div class="nd-metric-card-gradient"></div>
                <div class="d-flex align-items-center justify-content-between position-relative z-1">
                    <div class="nd-metric-icon">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                    <div class="text-end">
                        <div class="nd-metric-value"><?= number_format($totalSubmissions) ?></div>
                        <div class="nd-metric-label">Envios Recebidos</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending -->
        <div class="col-6 col-lg-4">
            <div class="nd-metric-card warning">
                <div class="nd-metric-card-gradient"></div>
                <div class="d-flex align-items-center justify-content-between position-relative z-1">
                    <div class="nd-metric-icon">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div class="text-end">
                        <div class="nd-metric-value"><?= number_format($pendingSubmissions) ?></div>
                        <div class="nd-metric-label">Aguardando Análise</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Approved -->
        <div class="col-6 col-lg-4">
            <div class="nd-metric-card success">
                <div class="nd-metric-card-gradient"></div>
                <div class="d-flex align-items-center justify-content-between position-relative z-1">
                    <div class="nd-metric-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="text-end">
                        <div class="nd-metric-value"><?= number_format($approvedSubmissions) ?></div>
                        <div class="nd-metric-label">Aprovados</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rejected -->
        <div class="col-6 col-lg-4">
            <div class="nd-metric-card danger">
                <div class="nd-metric-card-gradient"></div>
                <div class="d-flex align-items-center justify-content-between position-relative z-1">
                    <div class="nd-metric-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="text-end">
                        <div class="nd-metric-value"><?= number_format($rejectedSubmissions) ?></div>
                        <div class="nd-metric-label">Rejeitados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Portal Users -->
        <div class="col-6 col-lg-4">
            <div class="nd-metric-card gold">
                <div class="nd-metric-card-gradient"></div>
                <div class="d-flex align-items-center justify-content-between position-relative z-1">
                    <div class="nd-metric-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="text-end">
                        <div class="nd-metric-value"><?= number_format($totalPortalUsers) ?></div>
                        <div class="nd-metric-label">Usuários Cadastrados</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Published Documents -->
        <div class="col-6 col-lg-4">
            <div class="nd-metric-card info">
                <div class="nd-metric-card-gradient"></div>
                <div class="d-flex align-items-center justify-content-between position-relative z-1">
                    <div class="nd-metric-icon">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="text-end">
                        <div class="nd-metric-value"><?= number_format($publishedDocuments) ?></div>
                        <div class="nd-metric-label">Documentos Vigentes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Recent Submissions -->
        <div class="col-lg-8">
            <div class="nd-card h-100">
                <div class="nd-card-header d-flex align-items-center justify-content-between">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-inbox me-2"></i>
                        Envios Recentes
                    </h5>
                    <a href="/admin/submissions" class="nd-btn nd-btn-ghost nd-btn-sm">
                        Ver Todas
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="nd-card-body p-0">
                    <?php if (!$recentSubmissions): ?>
                        <div class="text-center py-5">
                            <div class="nd-empty-icon mb-3">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="text-muted mb-0">Nenhuma submissão recente encontrada.</p>
                        </div>
                    <?php else: ?>
                        <div class="nd-table-wrapper" style="border: none; border-radius: 0;">
                            <table class="nd-table">
                                <thead>
                                    <tr>
                                        <th>Solicitante</th>
                                        <th>Data de Envio</th>
                                        <th>Situação</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSubmissions as $s): ?>
                                        <?php
                                        $statusConfig = match($s['status'] ?? '') {
                                            'PENDING'       => ['label' => 'Pendente', 'class' => 'warning', 'icon' => 'bi-clock'],
                                            'UNDER_REVIEW'  => ['label' => 'Em Análise', 'class' => 'info', 'icon' => 'bi-search'],
                                            'APPROVED'      => ['label' => 'Aprovada', 'class' => 'success', 'icon' => 'bi-check-circle'],
                                            'COMPLETED'     => ['label' => 'Concluída', 'class' => 'success', 'icon' => 'bi-check-all'],
                                            'REJECTED'      => ['label' => 'Rejeitada', 'class' => 'danger', 'icon' => 'bi-x-circle'],
                                            default         => ['label' => $s['status'] ?? '-', 'class' => 'neutral', 'icon' => 'bi-dash']
                                        };
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="nd-avatar nd-avatar-sm">
                                                        <?= strtoupper(substr($s['user_name'] ?? 'U', 0, 1)) ?>
                                                    </div>
                                                    <span class="fw-medium text-dark"><?= htmlspecialchars($s['user_name'] ?? 'Usuário', ENT_QUOTES) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?= date('d/m/Y H:i', strtotime($s['submitted_at'] ?? 'now')) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="nd-badge nd-badge-<?= $statusConfig['class'] ?>">
                                                    <i class="bi <?= $statusConfig['icon'] ?>"></i>
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
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar: Activity & Alerts -->
        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="nd-card mb-4">
                <div class="nd-card-header d-flex align-items-center justify-content-between">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-activity me-2"></i>
                        Últimas Atividades
                    </h5>
                    <a href="/admin/audit-logs" class="nd-btn nd-btn-link nd-btn-sm p-0 text-decoration-none">
                        Ver Todas
                    </a>
                </div>
                <div class="nd-card-body p-0">
                    <?php if (!$recentLogs): ?>
                        <div class="p-4 text-center text-muted small">Nenhuma atividade recente registrada.</div>
                    <?php else: ?>
                        <div class="nd-activity-list">
                            <?php foreach (array_slice($recentLogs, 0, 5) as $log): ?>
                                <?php
                                    $actionKey = $log['action'] ?? 'UNKNOWN';
                                    
                                    $actionText = match($actionKey) {
                                        'LOGIN_SUCCESS' => 'Acesso realizado',
                                        'LOGIN_FAILED' => 'Tentativa de acesso falhou',
                                        'PORTAL_USER_CREATED' => 'Usuário cadastrado',
                                        'PORTAL_USER_UPDATED' => 'Dados atualizados',
                                        'PORTAL_ACCESS_LINK_GENERATED' => 'Link de acesso gerado',
                                        'SUBMISSION_CREATED' => 'Nova submissão',
                                        'PORTAL_SUBMISSION_CREATED' => 'Submissão via Portal',
                                        default => ucwords(strtolower(str_replace('_', ' ', $actionKey)))
                                    };

                                    $iconClass = 'bi-circle-fill';
                                    $colorClass = 'text-secondary';
                                    
                                    if (str_contains($actionKey, 'LOGIN_SUCCESS')) {
                                        $iconClass = 'bi-check-circle-fill'; $colorClass = 'text-success';
                                    } elseif (str_contains($actionKey, 'FAILED')) {
                                        $iconClass = 'bi-x-circle-fill'; $colorClass = 'text-danger';
                                    } elseif (str_contains($actionKey, 'CREATED')) {
                                        $iconClass = 'bi-plus-circle-fill'; $colorClass = 'text-primary';
                                    } elseif (str_contains($actionKey, 'UPDATED')) {
                                        $iconClass = 'bi-pencil-fill'; $colorClass = 'text-info';
                                    }
                                ?>
                                <div class="nd-activity-item">
                                    <div class="nd-activity-icon <?= $colorClass ?>">
                                        <i class="bi <?= $iconClass ?>"></i>
                                    </div>
                                    <div class="nd-activity-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="nd-activity-title"><?= $actionText ?></span>
                                            <small class="nd-activity-time"><?= date('H:i', strtotime($log['occurred_at'])) ?></small>
                                        </div>
                                        <p class="nd-activity-summary"><?= htmlspecialchars($log['summary'] ?? '', ENT_QUOTES) ?></p>
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
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle text-success fs-2 mb-2 d-block"></i>
                            <span class="text-muted small">Tudo em ordem! Nenhum alerta pendente.</span>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php if (!empty($a['oldPending'])): ?>
                                <a href="/admin/submissions?status=PENDING" class="nd-alert-card nd-alert-card-warning">
                                    <div class="nd-alert-card-icon">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div class="nd-alert-card-content">
                                        <strong><?= (int)$a['oldPending'] ?> envios</strong>
                                        <span>aguardando há mais de 7 dias</span>
                                    </div>
                                    <i class="bi bi-chevron-right nd-alert-card-arrow"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($a['expiredTokens'])): ?>
                                <a href="/admin/tokens" class="nd-alert-card nd-alert-card-danger">
                                    <div class="nd-alert-card-icon">
                                        <i class="bi bi-shield-x"></i>
                                    </div>
                                    <div class="nd-alert-card-content">
                                        <strong><?= (int)$a['expiredTokens'] ?> tokens</strong>
                                        <span>de acesso expirados</span>
                                    </div>
                                    <i class="bi bi-chevron-right nd-alert-card-arrow"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($a['inactiveUsers30'])): ?>
                                <a href="/admin/portal-users" class="nd-alert-card nd-alert-card-neutral">
                                    <div class="nd-alert-card-icon">
                                        <i class="bi bi-person-dash"></i>
                                    </div>
                                    <div class="nd-alert-card-content">
                                        <strong><?= (int)$a['inactiveUsers30'] ?> usuários</strong>
                                        <span>inativos há 30+ dias</span>
                                    </div>
                                    <i class="bi bi-chevron-right nd-alert-card-arrow"></i>
                                </a>
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
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribuição por Situação
                    </h5>
                </div>
                <div class="nd-card-body">
                    <canvas id="chartStatus" height="220"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="nd-card">
                <div class="nd-card-header">
                    <h5 class="nd-card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Volume de Envios (30 Dias)
                    </h5>
                </div>
                <div class="nd-card-body">
                    <canvas id="chartDaily" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Styles -->
<style>
    .nd-page-title {
        font-family: var(--nd-font-heading);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--nd-navy-800);
        margin: 0;
    }
    
    .nd-page-subtitle {
        font-size: 0.875rem;
        color: var(--nd-gray-500);
        margin: 0.25rem 0 0;
    }
    
    .nd-empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        background: var(--nd-surface-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nd-empty-icon i {
        font-size: 2rem;
        color: var(--nd-gray-300);
    }
    
    /* Activity List */
    .nd-activity-list {
        padding: 0;
    }
    
    .nd-activity-item {
        display: flex;
        gap: 0.875rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--nd-surface-100);
        transition: background 0.15s ease;
    }
    
    .nd-activity-item:last-child {
        border-bottom: none;
    }
    
    .nd-activity-item:hover {
        background: var(--nd-surface-50);
    }
    
    .nd-activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--nd-surface-100);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.75rem;
    }
    
    .nd-activity-content {
        flex: 1;
        min-width: 0;
    }
    
    .nd-activity-title {
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--nd-gray-800);
    }
    
    .nd-activity-time {
        font-size: 0.6875rem;
        color: var(--nd-gray-400);
    }
    
    .nd-activity-summary {
        font-size: 0.75rem;
        color: var(--nd-gray-500);
        margin: 0.25rem 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Alert Cards */
    .nd-alert-card {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.875rem 1rem;
        border-radius: var(--nd-radius-lg);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .nd-alert-card:hover {
        transform: translateX(4px);
    }
    
    .nd-alert-card-warning {
        background: rgba(245, 158, 11, 0.08);
        color: var(--nd-warning-dark);
    }
    
    .nd-alert-card-danger {
        background: rgba(239, 68, 68, 0.08);
        color: var(--nd-danger-dark);
    }
    
    .nd-alert-card-neutral {
        background: rgba(100, 116, 139, 0.08);
        color: var(--nd-gray-600);
    }
    
    .nd-alert-card-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--nd-radius);
        background: currentColor;
        opacity: 0.15;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nd-alert-card-icon i {
        opacity: 1;
        font-size: 1rem;
    }
    
    .nd-alert-card-content {
        flex: 1;
        font-size: 0.8125rem;
        line-height: 1.4;
    }
    
    .nd-alert-card-content strong {
        display: block;
    }
    
    .nd-alert-card-content span {
        opacity: 0.8;
    }
    
    .nd-alert-card-arrow {
        opacity: 0.4;
    }
</style>

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
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Inter', size: 12 }
                    }
                }
            }
        }
    });

    // Chart 2 - Daily (Line with Gradient)
    const dailyLabels = (dailyData||[]).map(r => r.date.split('-').reverse().slice(0, 2).join('/'));
    const dailyValues = (dailyData||[]).map(r => r.total);
    
    const ctx = document.getElementById('chartDaily').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(212, 168, 75, 0.25)');
    gradient.addColorStop(1, 'rgba(212, 168, 75, 0.02)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Submissões',
                data: dailyValues,
                borderColor: '#d4a84b',
                backgroundColor: gradient,
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
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { family: 'Inter', size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        font: { family: 'Inter', size: 11 },
                        maxRotation: 0
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
})();
</script>