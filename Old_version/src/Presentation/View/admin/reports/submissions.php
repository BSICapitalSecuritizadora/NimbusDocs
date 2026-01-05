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
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Relatório de submissões</h1>
        <p class="text-muted small mb-0">
            Análise por período, status e usuário das submissões recebidas via portal.
        </p>
    </div>
    <a href="/admin/reports/submissions/export?<?= $queryExport ?>"
        class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-download"></i> Exportar CSV
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-2">
                <label class="form-label small mb-1">De</label>
                <input type="date" name="from_date" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Até</label>
                <input type="date" name="to_date" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <?php
                    $statusOptions = [
                        ''           => 'Todos',
                        'PENDENTE'   => 'Pendente',
                        'FINALIZADA' => 'Finalizada',
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
            <div class="col-md-3">
                <label class="form-label small mb-1">E-mail do usuário</label>
                <input type="text" name="email" class="form-control form-control-sm"
                    placeholder="contato@cliente.com"
                    value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">Aplicar filtros</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="small text-muted mb-1">Total de submissões</div>
                <div class="fs-4 fw-semibold"><?= $kpis['total'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="small text-muted mb-1">Pendentes</div>
                <div class="fs-4 fw-semibold text-warning"><?= $kpis['pending'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="small text-muted mb-1">Finalizadas</div>
                <div class="fs-4 fw-semibold text-success"><?= $kpis['approved'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="small text-muted mb-1">Rejeitadas</div>
                <div class="fs-4 fw-semibold text-danger"><?= $kpis['rejected'] ?? 0 ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">Submissões por dia (período filtrado)</h2>
                <canvas id="chartSubmissionsByDay" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">Usuários com mais submissões</h2>
                <?php if (!$ranking): ?>
                    <p class="text-muted small mb-0">Nenhuma submissão no período.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranking as $row): ?>
                                    <tr>
                                        <td class="small">
                                            <?= htmlspecialchars($row['full_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                                            <span class="text-muted">
                                                <?= htmlspecialchars($row['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>
                                        <td class="small text-end fw-semibold">
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

<div class="card">
    <div class="card-body">
        <h2 class="h6 mb-3">Submissões (detalhamento)</h2>

        <?php if (!$submissions): ?>
            <p class="text-muted small mb-0">
                Nenhuma submissão encontrada para os filtros informados.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Título</th>
                            <th>Status</th>
                            <th>Criado em</th>
                            <th>Atualizado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td>#<?= (int)$s['id'] ?></td>
                                <td class="small">
                                    <?= htmlspecialchars($s['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                                    <span class="text-muted">
                                        <?= htmlspecialchars($s['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <?= htmlspecialchars($s['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="small">
                                    <span class="badge 
                                    <?php
                                    $st = $s['status'] ?? '';
                                    if ($st === 'PENDENTE')   echo 'bg-warning text-dark';
                                    elseif ($st === 'FINALIZADA') echo 'bg-success';
                                    elseif ($st === 'REJEITADA')  echo 'bg-danger';
                                    else echo 'bg-secondary';
                                    ?>">
                                        <?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?= htmlspecialchars($s['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="small text-muted">
                                    <?= htmlspecialchars($s['updated_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
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
                    data: values,
                    fill: false,
                    tension: 0.2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
</script>