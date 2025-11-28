<?php

/** @var int $totalSubmissions */
/** @var int $pendingSubmissions */
/** @var int $finishedSubmissions */
/** @var int $totalPortalUsers */
/** @var int $validTokens */
/** @var int $expiredTokens */
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
                    <div class="fs-3 fw-bold"><?= $finishedSubmissions ?></div>
                    <div class="small">Finalizadas</div>
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
            <div class="card text-bg-info shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $validTokens ?></div>
                    <div class="small">Tokens Ativos</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-bg-secondary shadow-sm">
                <div class="card-body">
                    <div class="fs-3 fw-bold"><?= $expiredTokens ?></div>
                    <div class="small">Tokens Expirados</div>
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

                </div>
            </div>
        </div>

    </div>

</div>