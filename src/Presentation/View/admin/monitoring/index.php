<?php
/**
 * Dashboard de Monitoramento Avançado
 * Disponível em $data (stats, recentRequests, alerts, etc)
 */
?>
<!-- Header e Actions -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
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
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="nd-card h-100 border-0 shadow-sm transition-fast hover-shadow position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                         <div class="text-muted small text-uppercase fw-bold ls-1">Volume de Acessos</div>
                         <div class="h2 fw-bold mb-0 text-dark mt-1"><?php echo $data['stats']['total_requests']; ?></div>
                    </div>
                    <div class="rounded-circle bg-success-subtle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-bar-chart-fill text-success fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 small text-success fw-medium d-flex align-items-center">
                    <i class="bi bi-arrow-up-short fs-5"></i>
                    <span><?php echo $data['stats']['success']; ?> processados com êxito</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="nd-card h-100 border-0 shadow-sm transition-fast hover-shadow position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                         <div class="text-muted small text-uppercase fw-bold ls-1">Disponibilidade</div>
                         <div class="h2 fw-bold mb-0 text-dark mt-1"><?php echo $data['successRate']; ?>%</div>
                    </div>
                     <div class="rounded-circle bg-info-subtle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-activity text-info fs-5"></i>
                    </div>
                </div>
                 <div class="progress mt-3 bg-light" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: <?php echo $data['successRate']; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="nd-card h-100 border-0 shadow-sm transition-fast hover-shadow position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                         <div class="text-muted small text-uppercase fw-bold ls-1">Falhas Técnicas</div>
                         <div class="h2 fw-bold mb-0 text-dark mt-1"><?php echo $data['stats']['errors']; ?></div>
                    </div>
                     <div class="rounded-circle bg-danger-subtle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-bug-fill text-danger fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 small text-danger fw-medium d-flex align-items-center">
                     <i class="bi bi-exclamation-triangle me-1"></i>
                     <span><?php echo $data['errorRate']; ?>% de taxa de erro</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="nd-card h-100 border-0 shadow-sm transition-fast hover-shadow position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                         <div class="text-muted small text-uppercase fw-bold ls-1">Latência Média</div>
                         <div class="h2 fw-bold mb-0 text-dark mt-1"><?php echo $data['stats']['avg_duration_ms']; ?>ms</div>
                    </div>
                     <div class="rounded-circle bg-warning-subtle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted fw-medium d-flex align-items-center">
                     <span class="text-warning me-1"><i class="bi bi-circle-fill" style="font-size: 6px;"></i></span>
                     <span><?php echo $data['stats']['slow_requests']; ?> requisições lentas</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas -->
<div class="nd-card mb-4 border-0 shadow-sm">
    <div class="nd-card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-bell-fill text-muted"></i>
            <h6 class="nd-card-title mb-0">Alertas de Conformidade</h6>
        </div>
        <div class="btn-group btn-group-sm">
            <a href="/admin/monitoring?filter=all" class="btn btn-sm <?php echo $data['filter'] === 'all' ? 'btn-dark' : 'btn-outline-secondary border-0 text-muted'; ?> rounded-pill px-3">Todos</a>
            <a href="/admin/monitoring?filter=errors" class="btn btn-sm <?php echo $data['filter'] === 'errors' ? 'btn-danger' : 'btn-outline-secondary border-0 text-muted'; ?> rounded-pill px-3">Erros</a>
            <a href="/admin/monitoring?filter=slow" class="btn btn-sm <?php echo $data['filter'] === 'slow' ? 'btn-warning' : 'btn-outline-secondary border-0 text-muted'; ?> rounded-pill px-3">Lentidão</a>
        </div>
    </div>
    <div class="nd-card-body p-0">
        <?php if (empty($data['alerts'])): ?>
            <div class="p-5 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 64px; height: 64px;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                </div>
                <h6 class="fw-bold text-dark">Tudo tranquilo por aqui!</h6>
                <p class="text-muted small mb-0">O sistema está operando dentro dos parâmetros ideais de segurança e performance.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($data['alerts'] as $alert): ?>
                    <div class="list-group-item p-3 border-light">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <?php if (($alert['type'] ?? '') === 'error'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill"><i class="bi bi-x-circle-fill me-1"></i> Falha Crítica</span>
                                    <?php elseif (($alert['type'] ?? '') === 'unauthorized'): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill"><i class="bi bi-shield-exclamation me-1"></i> Segurança</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border rounded-pill"><i class="bi bi-speedometer2 me-1"></i> Performance</span>
                                    <?php endif; ?>
                                    
                                    <span class="small text-muted">&bull;</span>
                                    <code class="text-dark fw-bold small px-2 py-1 bg-light rounded border">
                                        <?php echo htmlspecialchars($alert['method'] ?? 'GET'); ?> 
                                        <?php echo htmlspecialchars($alert['uri'] ?? '/'); ?>
                                    </code>
                                </div>
                                
                                <?php if (!empty($alert['error'])): ?>
                                    <div class="alert alert-light border-danger border-start border-3 rounded-0 py-2 px-3 small text-danger font-monospace mb-2 bg-light">
                                         <?php echo htmlspecialchars($alert['error']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex align-items-center gap-3 small text-muted">
                                    <div><i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars(isset($alert['timestamp']) ? date('d/m/Y H:i:s', strtotime($alert['timestamp'])) : '-'); ?></div>
                                    <div><i class="bi bi-globe me-1"></i> <?php echo htmlspecialchars($alert['ip'] ?? '-'); ?></div>
                                    <div><i class="bi bi-hourglass me-1"></i> <?php echo $alert['duration_ms'] ?? 0; ?>ms</div>
                                </div>
                            </div>
                            <small class="text-muted font-monospace opacity-50">#<?php echo htmlspecialchars(substr($alert['request_id'] ?? '', 0, 6)); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Top Endpoints -->
    <div class="col-lg-6">
        <div class="nd-card h-100 border-0 shadow-sm">
            <div class="nd-card-header bg-white border-bottom py-3">
                <h6 class="nd-card-title mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up-arrow text-primary"></i> Recursos Mais Consumidos
                </h6>
            </div>
            <div class="nd-card-body p-0">
                <div class="table-responsive">
                    <table class="nd-table table-hover mb-0">
                        <thead class="bg-light">
                             <tr>
                                <th class="ps-4 fw-semibold text-muted x-small text-uppercase">Endpoint</th>
                                <th class="pe-4 text-end fw-semibold text-muted x-small text-uppercase">Reqs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['stats']['top_endpoints'])): ?>
                                <?php foreach ($data['stats']['top_endpoints'] as $endpoint => $count): ?>
                                    <tr>
                                        <td class="ps-4"><code class="text-primary small bg-primary-subtle px-1 rounded"><?php echo htmlspecialchars(strlen($endpoint) > 40 ? substr($endpoint, 0, 37) . '...' : $endpoint); ?></code></td>
                                        <td class="pe-4 text-end fw-bold text-dark"><?php echo $count; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center text-muted p-3">Sem dados registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top IPs -->
    <div class="col-lg-6">
        <div class="nd-card h-100 border-0 shadow-sm">
            <div class="nd-card-header bg-white border-bottom py-3">
                <h6 class="nd-card-title mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-globe text-info"></i> Origens de Tráfego
                </h6>
            </div>
            <div class="nd-card-body p-0">
                 <div class="table-responsive">
                    <table class="nd-table table-hover mb-0">
                        <thead class="bg-light">
                             <tr>
                                <th class="ps-4 fw-semibold text-muted x-small text-uppercase">Endereço IP</th>
                                <th class="pe-4 text-end fw-semibold text-muted x-small text-uppercase">Acessos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['stats']['top_ips'])): ?>
                                <?php foreach ($data['stats']['top_ips'] as $ip => $count): ?>
                                    <tr>
                                        <td class="ps-4"><span class="font-monospace text-dark small"><i class="bi bi-laptop me-2 text-muted"></i><?php echo htmlspecialchars($ip); ?></span></td>
                                        <td class="pe-4 text-end fw-bold text-dark"><?php echo $count; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center text-muted p-3">Sem dados registrados</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Requisições -->
<div class="nd-card mt-4 border-0 shadow-sm">
    <div class="nd-card-header bg-white border-bottom py-3">
        <h6 class="nd-card-title mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-list-columns-reverse text-dark"></i> Registro de Transações Recentes
        </h6>
    </div>
    <div class="table-responsive">
        <table class="nd-table align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Estado</th>
                    <th>Operação</th>
                    <th>Recurso</th>
                    <th>Endereço IP</th>
                    <th>Duração</th>
                    <th class="pe-4">Horário</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['recentRequests'])): ?>
                    <?php foreach (array_slice($data['recentRequests'], 0, 15) as $req): ?>
                        <tr>
                            <td class="ps-4">
                                <?php 
                                $code = $req['status_code'] ?? 200;
                                $badgeClass = match(true) {
                                    $code >= 500 => 'nd-badge-danger',
                                    $code >= 400 => 'nd-badge-warning',
                                    $code >= 300 => 'nd-badge-secondary',
                                    default => 'nd-badge-success'
                                };
                                ?>
                                <span class="nd-badge <?php echo $badgeClass; ?> font-monospace"><?php echo $code; ?></span>
                            </td>
                            <td><span class="fw-bold small text-dark"><?php echo htmlspecialchars($req['method'] ?? 'GET'); ?></span></td>
                            <td>
                                <code class="text-muted small" title="<?php echo htmlspecialchars($req['uri'] ?? '/'); ?>">
                                    <?php 
                                    $uri = htmlspecialchars($req['uri'] ?? '/');
                                    echo strlen($uri) > 50 ? substr($uri, 0, 47) . '...' : $uri;
                                    ?>
                                </code>
                            </td>
                            <td class="small font-monospace text-muted"><?php echo htmlspecialchars($req['ip'] ?? '-'); ?></td>
                            <td class="small <?php echo ($req['duration_ms'] ?? 0) > 1000 ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                <?php 
                                    $dur = $req['duration_ms'] ?? 0;
                                    echo $dur < 1000 ? number_format($dur, 0) . 'ms' : number_format($dur/1000, 2) . 's';
                                ?>
                            </td>
                            <td class="pe-4 small text-muted">
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
