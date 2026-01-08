<?php
/**
 * Dashboard de Monitoramento Avançado
 * Disponível em $data (stats, recentRequests, alerts, etc)
 */
?>
<!-- Header e Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
            <i class="bi bi-speedometer2"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Saúde do Sistema</h1>
            <p class="text-muted mb-0 small">Indicadores de disponibilidade, performance e auditoria técnica (24h)</p>
        </div>
    </div>
    <button class="nd-btn nd-btn-outline nd-btn-sm" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i> Atualizar Dados
    </button>
</div>

<!-- Cards de KPIs -->
<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="nd-card h-100 border-start border-4 border-success">
            <div class="nd-card-body">
                <div class="h3 fw-bold mb-1" style="color: var(--nd-navy-900);"><?php echo $data['stats']['total_requests']; ?></div>
                <div class="text-muted small text-uppercase fw-semibold">Volume de Acessos</div>
                <div class="mt-2 small text-success">
                    <i class="bi bi-check-circle-fill me-1"></i> <?php echo $data['stats']['success']; ?> processados
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="nd-card h-100 border-start border-4 border-info">
            <div class="nd-card-body">
                <div class="h3 fw-bold mb-1" style="color: var(--nd-navy-900);"><?php echo $data['successRate']; ?>%</div>
                <div class="text-muted small text-uppercase fw-semibold">Taxa de Disponibilidade</div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: <?php echo $data['successRate']; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="nd-card h-100 border-start border-4 border-danger">
            <div class="nd-card-body">
                <div class="h3 fw-bold mb-1" style="color: var(--nd-navy-900);"><?php echo $data['stats']['errors']; ?></div>
                <div class="text-muted small text-uppercase fw-semibold">Falhas Técnicas</div>
                <div class="mt-2 small text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $data['errorRate']; ?>% de erro
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <div class="nd-card h-100 border-start border-4 border-warning">
            <div class="nd-card-body">
                <div class="h3 fw-bold mb-1" style="color: var(--nd-navy-900);"><?php echo $data['stats']['avg_duration_ms']; ?>ms</div>
                <div class="text-muted small text-uppercase fw-semibold">Latência Média</div>
                <div class="mt-2 small text-warning">
                    <i class="bi bi-hourglass-split me-1"></i> <?php echo $data['stats']['slow_requests']; ?> críticas
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-bell text-muted"></i>
            <h6 class="nd-card-title mb-0">Alertas de Conformidade</h6>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="/admin/monitoring?filter=all" class="nd-btn nd-btn-sm <?php echo $data['filter'] === 'all' ? 'nd-btn-primary' : 'nd-btn-outline'; ?>">Todos</a>
            <a href="/admin/monitoring?filter=errors" class="nd-btn nd-btn-sm <?php echo $data['filter'] === 'errors' ? 'nd-btn-primary' : 'nd-btn-outline'; ?>">Erros</a>
            <a href="/admin/monitoring?filter=slow" class="nd-btn nd-btn-sm <?php echo $data['filter'] === 'slow' ? 'nd-btn-primary' : 'nd-btn-outline'; ?>">Lentidão</a>
        </div>
    </div>
    <div class="nd-card-body p-0">
        <?php if (empty($data['alerts'])): ?>
            <div class="p-4 text-center text-muted">
                <i class="bi bi-shield-check display-4 d-block mb-3 text-success"></i>
                Sistema operando conforme parâmetros de segurança e performance.
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($data['alerts'] as $alert): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <?php if (($alert['type'] ?? '') === 'error'): ?>
                                        <span class="nd-badge nd-badge-danger">Falha Crítica</span>
                                    <?php elseif (($alert['type'] ?? '') === 'unauthorized'): ?>
                                        <span class="nd-badge nd-badge-warning">Segurança</span>
                                    <?php else: ?>
                                        <span class="nd-badge nd-badge-secondary">Performance</span>
                                    <?php endif; ?>
                                    
                                    <code class="text-dark fw-bold small">
                                        <?php echo htmlspecialchars($alert['method'] ?? 'GET'); ?> 
                                        <?php echo htmlspecialchars($alert['uri'] ?? '/'); ?>
                                    </code>
                                </div>
                                
                                <?php if (!empty($alert['error'])): ?>
                                    <div class="small text-danger mt-1 mb-1">
                                        <i class="bi bi-arrow-return-right me-1"></i>
                                        <?php echo htmlspecialchars($alert['error']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars(isset($alert['timestamp']) ? date('d/m/Y H:i:s', strtotime($alert['timestamp'])) : 'unknown'); ?> &bull;
                                    <i class="bi bi-globe me-1"></i> <?php echo htmlspecialchars($alert['ip'] ?? ''); ?> &bull;
                                    <span class="fw-semibold"><?php echo $alert['duration_ms'] ?? 0; ?>ms</span>
                                </div>
                            </div>
                            <small class="text-muted font-monospace">ID: <?php echo htmlspecialchars(substr($alert['request_id'] ?? '', 0, 6)); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Top Endpoints -->
    <div class="col-lg-6 mb-4">
        <div class="nd-card h-100">
            <div class="nd-card-header">
                <h6 class="nd-card-title mb-0">Recursos Mais Consumidos</h6>
            </div>
            <div class="nd-card-body p-0">
                <div class="table-responsive">
                    <table class="nd-table table-hover mb-0">
                        <tbody>
                            <?php if (!empty($data['stats']['top_endpoints'])): ?>
                                <?php foreach ($data['stats']['top_endpoints'] as $endpoint => $count): ?>
                                    <tr>
                                        <td><code class="text-muted"><?php echo htmlspecialchars(strlen($endpoint) > 40 ? substr($endpoint, 0, 37) . '...' : $endpoint); ?></code></td>
                                        <td class="text-end fw-bold"><?php echo $count; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td class="text-center text-muted p-3">Sem dados registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top IPs -->
    <div class="col-lg-6 mb-4">
        <div class="nd-card h-100">
            <div class="nd-card-header">
                <h6 class="nd-card-title mb-0">Origens de Tráfego</h6>
            </div>
            <div class="nd-card-body p-0">
                 <div class="table-responsive">
                    <table class="nd-table table-hover mb-0">
                        <tbody>
                            <?php if (!empty($data['stats']['top_ips'])): ?>
                                <?php foreach ($data['stats']['top_ips'] as $ip => $count): ?>
                                    <tr>
                                        <td><span class="font-monospace text-dark"><?php echo htmlspecialchars($ip); ?></span></td>
                                        <td class="text-end fw-bold"><?php echo $count; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td class="text-center text-muted p-3">Sem dados registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Requisições -->
<div class="nd-card">
    <div class="nd-card-header">
        <h6 class="nd-card-title mb-0">Registro de Transações Recentes</h6>
    </div>
    <div class="table-responsive">
        <table class="nd-table">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Operação</th>
                    <th>Recurso</th>
                    <th>Endereço IP</th>
                    <th>Duração</th>
                    <th>Horário</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['recentRequests'])): ?>
                    <?php foreach (array_slice($data['recentRequests'], 0, 15) as $req): ?>
                        <tr>
                            <td>
                                <?php 
                                $code = $req['status_code'] ?? 200;
                                $badgeClass = match(true) {
                                    $code >= 500 => 'nd-badge-danger',
                                    $code >= 400 => 'nd-badge-warning',
                                    $code >= 300 => 'nd-badge-secondary',
                                    default => 'nd-badge-success'
                                };
                                ?>
                                <span class="nd-badge <?php echo $badgeClass; ?>"><?php echo $code; ?></span>
                            </td>
                            <td><span class="fw-bold small"><?php echo htmlspecialchars($req['method'] ?? 'GET'); ?></span></td>
                            <td>
                                <code class="text-muted small" title="<?php echo htmlspecialchars($req['uri'] ?? '/'); ?>">
                                    <?php 
                                    $uri = htmlspecialchars($req['uri'] ?? '/');
                                    echo strlen($uri) > 50 ? substr($uri, 0, 47) . '...' : $uri;
                                    ?>
                                </code>
                            </td>
                            <td class="small font-monospace"><?php echo htmlspecialchars($req['ip'] ?? '-'); ?></td>
                            <td class="small <?php echo ($req['duration_ms'] ?? 0) > 1000 ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                <?php echo number_format($req['duration_ms'] ?? 0, 0); ?>ms
                            </td>
                            <td class="small text-muted">
                                <?php echo date('H:i:s', strtotime($req['timestamp'] ?? 'now')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted p-4">Nenhuma transação registrada recentemente</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    // Auto-refresh a cada 30 segundos
    setTimeout(() => {
        location.reload();
    }, 30000);
</script>
