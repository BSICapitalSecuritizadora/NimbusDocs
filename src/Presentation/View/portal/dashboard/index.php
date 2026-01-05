<?php

/** @var array $user */
/** @var int $total */
/** @var int $pendentes */
/** @var int $concluidas */
/** @var array $submissions */
/** @var array $announcements */

?>
<div class="row mb-4">
    <div class="col-12 col-lg-8">
        <h1 class="h4 mb-1">Olá, <?= htmlspecialchars($user['full_name'] ?? $user['email'], ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="text-muted mb-0">
            Aqui você acompanha seus envios e documentos disponibilizados pela BSI.
        </p>
    </div>
</div>

<?php if (!empty($announcements)): ?>
    <div class="mb-4">
        <?php foreach ($announcements as $a): ?>
            <?php
            $level = $a['level'] ?? 'info';
            $class = 'alert-info';
            if ($level === 'success') $class = 'alert-success';
            if ($level === 'warning') $class = 'alert-warning';
            if ($level === 'danger')  $class = 'alert-danger';
            ?>
            <div class="alert <?= $class ?> border-0 shadow-sm mb-2">
                <h2 class="h6 mb-1">
                    <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="small mb-0">
                    <?= nl2br(htmlspecialchars($a['body'], ENT_QUOTES, 'UTF-8')) ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="small text-muted mb-1">Total de envios</div>
                <div class="fs-4 fw-semibold"><?= $total ?></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="small text-muted mb-1">Pendentes</div>
                <div class="fs-4 fw-semibold text-warning"><?= $pendentes ?></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="small text-muted mb-1">Concluídos</div>
                <div class="fs-4 fw-semibold text-success"><?= $concluidas ?></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="small text-muted mb-1">Novo envio</div>
                <a href="/portal/submissions/new" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Criar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 mb-0">Meus envios recentes</h2>
            <a href="/portal/submissions" class="small">Ver todos</a>
        </div>

        <?php if (!$submissions): ?>
            <p class="text-muted mb-0">
                Você ainda não possui envios registrados.
                <a href="/portal/submissions/new">Clique aqui para fazer o primeiro envio.</a>
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Assunto / Título</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td>#<?= (int)$s['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($s['title'] ?? 'Envio', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        <?= htmlspecialchars($s['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $status = $s['status'] ?? '';
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'PENDENTE')   $badgeClass = 'bg-warning';
                                    if ($status === 'FINALIZADA') $badgeClass = 'bg-success';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>"
                                        class="btn btn-sm btn-outline-secondary">
                                        Detalhes
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