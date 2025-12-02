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

<div class="container-fluid">

    <h1 class="h4 mb-4">Dashboard</h1>

    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $totalSubmissions ?></div>
                    <div class="small">Total de Submissões</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $pendingSubmissions ?></div>
                    <div class="small">Pendentes</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $approvedSubmissions ?></div>
                    <div class="small">Submissões Aprovadas</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-bg-danger shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $rejectedSubmissions ?></div>
                    <div class="small">Submissões Rejeitadas</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-bg-dark shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $totalPortalUsers ?></div>
                    <div class="small">Usuários do Portal</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-bg-secondary shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $publishedDocuments ?></div>
                    <div class="small">Documentos Publicados</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Últimas Submissões</h5>

                    <?php if (!$recentSubmissions): ?>
                        <p class="text-muted mb-0">Nenhuma submissão recente.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
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
                                        <tr>
                                            <td><?= $s['id'] ?></td>
                                            <td><?= htmlspecialchars($s['user_name'] ?? 'Usuário', ENT_QUOTES) ?></td>
                                            <td><?= htmlspecialchars($s['submitted_at'] ?? '', ENT_QUOTES) ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($s['status'] ?? '', ENT_QUOTES) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/admin/submissions/<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary">Ver</a>
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

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Últimas Atividades</h5>

                    <?php if (!$recentLogs): ?>
                        <p class="text-muted">Nenhuma atividade registrada.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recentLogs as $log): ?>
                                <li class="list-group-item small">
                                    <strong><?= $log['action'] ?></strong><br>
                                    <span class="text-muted"><?= $log['occurred_at'] ?></span><br>
                                    <?= htmlspecialchars($log['summary'] ?? '', ENT_QUOTES) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>


                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="mb-3">Submissões por Status</h5>
                                    <canvas id="chartStatus" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="mb-3">Submissões por Dia (30 dias)</h5>
                                    <canvas id="chartDaily" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="mb-3">Documentos Publicados por Mês</h5>
                                    <canvas id="chartDocsMonth" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
                    <script>
                    (function(){
                        const statusData = <?= json_encode($chartStatusCounts ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
                        const dailyData  = <?= json_encode($chartDailyCounts ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
                        const docsMonth  = <?= json_encode($chartDocsPerMonth ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

                        // Chart 1 - Status
                        const statusLabels = ['Aprovado','Rejeitado','Pendente','Em análise'];
                        const statusKeys   = ['APPROVED','REJECTED','PENDING','IN_REVIEW'];
                        const statusValues = statusKeys.map(k => statusData[k] ?? 0);
                        new Chart(document.getElementById('chartStatus'), {
                            type: 'doughnut',
                            data: {
                                labels: statusLabels,
                                datasets: [{
                                    data: statusValues,
                                    backgroundColor: ['#198754','#dc3545','#ffc107','#0dcaf0'],
                                }]
                            }
                        });

                        // Chart 2 - Daily (last 30 days)
                        const dailyLabels = (dailyData||[]).map(r => r.date);
                        const dailyValues = (dailyData||[]).map(r => r.total);
                        new Chart(document.getElementById('chartDaily'), {
                            type: 'line',
                            data: {
                                labels: dailyLabels,
                                datasets: [{
                                    label: 'Submissões',
                                    data: dailyValues,
                                    borderColor: '#0d6efd',
                                    backgroundColor: 'rgba(13,110,253,0.2)',
                                    tension: 0.25,
                                    fill: true,
                                }]
                            },
                            options: {
                                scales: {
                                    y: { beginAtZero: true }
                                }
                            }
                        });

                        // Chart 3 - Documents per month
                        const docsLabels = (docsMonth||[]).map(r => r.month);
                        const docsValues = (docsMonth||[]).map(r => r.total);
                        new Chart(document.getElementById('chartDocsMonth'), {
                            type: 'bar',
                            data: {
                                labels: docsLabels,
                                datasets: [{
                                    label: 'Documentos',
                                    data: docsValues,
                                    backgroundColor: '#6c757d'
                                }]
                            },
                            options: {
                                scales: {
                                    y: { beginAtZero: true }
                                }
                            }
                        });
                    })();
                    </script>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Alertas Importantes</h5>
                    <?php $a = $alerts ?? []; ?>
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="card border-warning">
                                <div class="card-body py-2">
                                    <div class="fw-bold text-warning">Submissões > 7 dias</div>
                                    <div class="small"><?= (int)($a['oldPending'] ?? 0) ?> pendentes / em análise</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-danger">
                                <div class="card-body py-2">
                                    <div class="fw-bold text-danger">Tokens expirados</div>
                                    <div class="small"><?= (int)($a['expiredTokens'] ?? 0) ?> encontrados</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-warning">
                                <div class="card-body py-2">
                                    <div class="fw-bold text-warning">Documentos muito grandes</div>
                                    <div class="small"><?= (int)($a['veryLargeDocs'] ?? 0) ?> acima de 50MB</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-danger">
                                <div class="card-body py-2">
                                    <div class="fw-bold text-danger">Usuários inativos +30 dias</div>
                                    <div class="small"><?= (int)($a['inactiveUsers30'] ?? 0) ?> usuários</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>