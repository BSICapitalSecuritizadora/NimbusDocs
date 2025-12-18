<?php
/**
 * Dashboard de Monitoramento Avançado
 * Exibe estatísticas, alertas e performance de requisições em tempo real
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento Avançado - NimbusDocs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #0dcaf0;
        }
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .stat-card {
            border-top: 4px solid;
        }
        .stat-card.success {
            border-top-color: var(--success);
        }
        .stat-card.danger {
            border-top-color: var(--danger);
        }
        .stat-card.info {
            border-top-color: var(--info);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-custom {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
        .alert-item {
            border-left: 4px solid;
            border-radius: 0 8px 8px 0;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        .alert-item.error {
            border-left-color: var(--danger);
            background: rgba(220, 53, 69, 0.05);
        }
        .alert-item.unauthorized {
            border-left-color: var(--warning);
            background: rgba(255, 193, 7, 0.05);
        }
        .alert-item.slow {
            border-left-color: var(--info);
            background: rgba(13, 202, 240, 0.05);
        }
        .badge-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-status.success {
            background: rgba(25, 135, 84, 0.2);
            color: var(--success);
        }
        .badge-status.error {
            background: rgba(220, 53, 69, 0.2);
            color: var(--danger);
        }
        .badge-status.warning {
            background: rgba(255, 193, 7, 0.2);
            color: #ff9800;
        }
        .filter-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .endpoint-badge {
            display: inline-block;
            background: #f0f0f0;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-family: monospace;
            margin-right: 6px;
        }
        .duration-slow {
            color: var(--danger);
            font-weight: 600;
        }
        .duration-normal {
            color: var(--success);
        }
        .spinner-mini {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            stroke-dasharray: 314;
            stroke: #667eea;
        }
        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">
                <i class="fas fa-chart-line"></i> NimbusDocs Monitoring
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/monitoring">
                            <i class="fas fa-bell"></i> Monitoramento
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-0">
                            <i class="fas fa-tachometer-alt"></i> Monitoramento Avançado
                        </h1>
                        <small class="text-muted">Últimas 24 horas</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="stat-value"><?php echo $data['stats']['total_requests']; ?></div>
                        <div class="stat-label">Total de Requisições</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-check-circle text-success"></i> 
                                <?php echo $data['stats']['success']; ?> sucesso
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card info">
                    <div class="card-body">
                        <div class="stat-value"><?php echo $data['successRate']; ?>%</div>
                        <div class="stat-label">Taxa de Sucesso</div>
                        <div class="mt-2">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" style="width: <?php echo $data['successRate']; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card danger">
                    <div class="card-body">
                        <div class="stat-value"><?php echo $data['stats']['errors']; ?></div>
                        <div class="stat-label">Erros Detectados</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-times-circle text-danger"></i> 
                                <?php echo $data['errorRate']; ?>% da taxa
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                <div class="card stat-card info">
                    <div class="card-body">
                        <div class="stat-value"><?php echo $data['stats']['avg_duration_ms']; ?>ms</div>
                        <div class="stat-label">Tempo Médio</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-tachometer-alt text-info"></i> 
                                <?php echo $data['stats']['slow_requests']; ?> lento
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Alertas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-bell"></i> Alertas & Problemas
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Filtrar por tipo:</small>
                            <div class="btn-group" role="group">
                                <a href="/admin/monitoring?filter=all" class="filter-btn <?php echo $data['filter'] === 'all' ? 'active' : ''; ?>">
                                    Todos (<?php echo count($data['alerts']); ?>)
                                </a>
                                <a href="/admin/monitoring?filter=errors" class="filter-btn <?php echo $data['filter'] === 'errors' ? 'active' : ''; ?>">
                                    Erros (<?php echo $data['stats']['errors']; ?>)
                                </a>
                                <a href="/admin/monitoring?filter=unauthorized" class="filter-btn <?php echo $data['filter'] === 'unauthorized' ? 'active' : ''; ?>">
                                    Acesso Negado (<?php echo $data['stats']['unauthorized']; ?>)
                                </a>
                                <a href="/admin/monitoring?filter=slow" class="filter-btn <?php echo $data['filter'] === 'slow' ? 'active' : ''; ?>">
                                    Lentos (<?php echo $data['stats']['slow_requests']; ?>)
                                </a>
                            </div>
                        </div>

                        <!-- Lista de Alertas -->
                        <div class="mt-3">
                            <?php if (empty($data['alerts'])): ?>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-check-circle"></i> Nenhum alerta no período selecionado
                                </div>
                            <?php else: ?>
                                <?php foreach ($data['alerts'] as $alert): ?>
                                    <div class="alert-item <?php echo $alert['type'] ?? 'info'; ?>">
                                        <div class="d-flex justify-content-between align-items-start p-3">
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge badge-status <?php echo $alert['type'] ?? 'info'; ?>">
                                                        <?php echo match($alert['type'] ?? '') {
                                                            'error' => '❌ Erro',
                                                            'unauthorized' => '🔒 Acesso Negado',
                                                            default => '⚡ Lento'
                                                        }; ?>
                                                    </span>
                                                    <span class="endpoint-badge ms-2"><?php echo htmlspecialchars($alert['method'] ?? 'GET'); ?> <?php echo htmlspecialchars($alert['uri'] ?? '/'); ?></span>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> <?php echo htmlspecialchars($alert['timestamp'] ?? 'unknown'); ?> | 
                                                    <i class="fas fa-globe"></i> <?php echo htmlspecialchars($alert['ip'] ?? 'unknown'); ?> |
                                                    <strong><?php echo $alert['duration_ms'] ?? 0; ?>ms</strong>
                                                </small>
                                                <?php if (!empty($alert['error'])): ?>
                                                    <div class="mt-2">
                                                        <code style="font-size: 0.85rem; color: var(--danger);">
                                                            <?php echo htmlspecialchars($alert['error']); ?>
                                                        </code>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted text-end">ID: <?php echo htmlspecialchars(substr($alert['request_id'] ?? '', 0, 8)); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Tabelas -->
        <div class="row mb-4">
            <!-- Top Endpoints -->
            <div class="col-12 col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-road"></i> Endpoints Mais Acessados
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <tbody>
                                    <?php if (!empty($data['stats']['top_endpoints'])): ?>
                                        <?php foreach ($data['stats']['top_endpoints'] as $endpoint => $count): ?>
                                            <tr>
                                                <td>
                                                    <code style="font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars(strlen($endpoint) > 50 ? substr($endpoint, 0, 47) . '...' : $endpoint); ?>
                                                    </code>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-primary"><?php echo $count; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td class="text-muted text-center py-3">Sem dados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top IPs -->
            <div class="col-12 col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-network-wired"></i> IPs Mais Ativos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <tbody>
                                    <?php if (!empty($data['stats']['top_ips'])): ?>
                                        <?php foreach ($data['stats']['top_ips'] as $ip => $count): ?>
                                            <tr>
                                                <td>
                                                    <code style="font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars($ip); ?>
                                                    </code>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-info"><?php echo $count; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td class="text-muted text-center py-3">Sem dados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requisições Recentes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> Requisições Recentes
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive mb-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">Horário</th>
                                        <th style="width: 60px;">Status</th>
                                        <th>Endpoint</th>
                                        <th style="width: 120px;">IP</th>
                                        <th style="width: 100px;">Duração</th>
                                        <th style="width: 150px;">Usuário</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['recentRequests'])): ?>
                                        <?php foreach (array_slice($data['recentRequests'], 0, 20) as $req): ?>
                                            <tr>
                                                <td class="nowrap small">
                                                    <i class="fas fa-clock text-muted"></i> 
                                                    <?php echo date('H:i:s', strtotime($req['timestamp'] ?? 'now')); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $statusCode = $req['status_code'] ?? 200;
                                                    if ($statusCode >= 400) {
                                                        echo '<span class="badge bg-danger">' . $statusCode . '</span>';
                                                    } elseif ($statusCode >= 300) {
                                                        echo '<span class="badge bg-warning">' . $statusCode . '</span>';
                                                    } else {
                                                        echo '<span class="badge bg-success">' . $statusCode . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <code style="font-size: 0.85rem;">
                                                        <?php 
                                                        $uri = htmlspecialchars($req['uri'] ?? '/');
                                                        echo strlen($uri) > 40 ? substr($uri, 0, 37) . '...' : $uri;
                                                        ?>
                                                    </code>
                                                </td>
                                                <td class="small">
                                                    <i class="fas fa-globe text-muted"></i> 
                                                    <code><?php echo htmlspecialchars($req['ip'] ?? 'unknown'); ?></code>
                                                </td>
                                                <td class="small <?php echo ($req['duration_ms'] ?? 0) > 2000 ? 'duration-slow' : 'duration-normal'; ?>">
                                                    <?php echo number_format($req['duration_ms'] ?? 0, 0); ?>ms
                                                </td>
                                                <td class="small text-muted">
                                                    <i class="fas fa-user"></i> 
                                                    <?php echo htmlspecialchars($req['user'] ?? 'anônimo'); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox"></i> Nenhuma requisição registrada
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rodapé -->
        <div class="row mb-4">
            <div class="col-12">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Dashboard atualizado em tempo real. 
                    Os dados são armazenados em <code>storage/logs/requests.jsonl</code>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh a cada 30 segundos
        setTimeout(() => {
            location.reload();
        }, 30000);

        // Função para carregar dados via API (opcional para atualizações em tempo real)
        async function loadStatsApi() {
            try {
                const response = await fetch('/admin/monitoring/api/stats?hours=24');
                const data = await response.json();
                console.log('Stats:', data);
                // Aqui você pode atualizar elementos da página com os novos dados
            } catch (error) {
                console.error('Erro ao carregar stats:', error);
            }
        }

        // Carrega a cada 10 segundos
        setInterval(loadStatsApi, 10000);
    </script>
</body>
</html>
