<?php

/** @var array $user */
/** @var int $total */
/** @var int $pendentes */
/** @var int $concluidas */
/** @var array $submissions */
/** @var array $announcements */

?>
<?php

/** @var array $user */
/** @var int $total */
/** @var int $pendentes */
/** @var int $concluidas */
/** @var array $submissions */
/** @var array $announcements */

?>
<div class="row mb-4 align-items-center">
    <div class="col-12 col-lg-8">
        <h1 class="h3 fw-bold text-dark mb-1">Olá, <?= htmlspecialchars($user['full_name'] ?? $user['email'], ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="text-secondary mb-0">
            Bem-vindo ao seu portal exclusivo. Acompanhe seus envios e documentos.
        </p>
    </div>
    <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
        <a href="/portal/submissions/new" class="nd-btn nd-btn-gold shadow-sm">
            <i class="bi bi-plus-lg"></i> Novo Envio
        </a>
    </div>
</div>

<?php if (!empty($announcements)): ?>
    <div class="mb-5">
        <?php foreach ($announcements as $a): ?>
            <?php
            $level = $a['level'] ?? 'info';
            $class = 'nd-alert-info';
            $icon  = 'bi-info-circle-fill';
            
            if ($level === 'success') { $class = 'nd-alert-success'; $icon = 'bi-check-circle-fill'; }
            if ($level === 'warning') { $class = 'nd-alert-warning'; $icon = 'bi-exclamation-triangle-fill'; }
            if ($level === 'danger')  { $class = 'nd-alert-danger';  $icon = 'bi-x-circle-fill'; }
            ?>
            <div class="nd-alert <?= $class ?> shadow-sm mb-3">
                <i class="bi <?= $icon ?> fs-5"></i>
                <div class="nd-alert-text">
                    <h5 class="nd-alert-card-title mb-1">
                        <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h5>
                    <span>
                        <?= nl2br(htmlspecialchars($a['body'], ENT_QUOTES, 'UTF-8')) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row g-4 mb-5">
    <!-- Total -->
    <div class="col-12 col-md-4">
        <div class="nd-metric-card primary h-100">
            <div class="nd-metric-card-gradient"></div>
            <div class="d-flex justify-content-between align-items-start position-relative">
                <div>
                    <div class="nd-metric-value mb-1"><?= $total ?></div>
                    <div class="nd-metric-label">Total de Envios</div>
                </div>
                <div class="nd-metric-icon">
                    <i class="bi bi-layers-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendentes -->
    <div class="col-12 col-md-4">
        <div class="nd-metric-card warning h-100">
            <div class="nd-metric-card-gradient"></div>
            <div class="d-flex justify-content-between align-items-start position-relative">
                <div>
                    <div class="nd-metric-value mb-1"><?= $pendentes ?></div>
                    <div class="nd-metric-label">Aguardando Análise</div>
                </div>
                <div class="nd-metric-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Concluídos -->
    <div class="col-12 col-md-4">
        <div class="nd-metric-card success h-100">
            <div class="nd-metric-card-gradient"></div>
            <div class="d-flex justify-content-between align-items-start position-relative">
                <div>
                    <div class="nd-metric-value mb-1"><?= $concluidas ?></div>
                    <div class="nd-metric-label">Processados / Concluídos</div>
                </div>
                <div class="nd-metric-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nd-card mb-4">
    <div class="nd-card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 px-4 pb-2">
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-primary" style="width: 32px; height: 32px;">
                <i class="bi bi-clock-history"></i>
            </div>
            <h2 class="nd-card-title fs-5">Envios Recentes</h2>
        </div>
        <a href="/portal/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
            Ver Todos
        </a>
    </div>
    
    <div class="nd-card-body p-0">
        <?php if (!$submissions): ?>
            <div class="text-center py-5">
                <div class="mb-3 text-muted opacity-25">
                    <i class="bi bi-inbox fs-1"></i>
                </div>
                <h6 class="text-secondary fw-normal mb-1">Nenhum envio encontrado</h6>
                <p class="small text-muted mb-3">Você ainda não realizou nenhum envio de documento.</p>
                <a href="/portal/submissions/new" class="nd-btn nd-btn-primary nd-btn-sm">
                    <i class="bi bi-plus-lg"></i> Criar Primeiro Envio
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Assunto / Título</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td class="ps-4 fw-medium text-muted">#<?= str_pad((string)$s['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <span class="d-block fw-medium text-dark">
                                        <?= htmlspecialchars($s['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-secondary">
                                        <i class="bi bi-calendar3 small"></i>
                                        <?= date('d/m/Y', strtotime($s['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $status = $s['status'] ?? '';
                                    $badgeClass = 'nd-badge-secondary';
                                    $icon = 'bi-circle';
                                    
                                    if ($status === 'PENDENTE')   { $badgeClass = 'nd-badge-warning'; $icon = 'bi-hourglass'; }
                                    if ($status === 'FINALIZADA') { $badgeClass = 'nd-badge-success'; $icon = 'bi-check2'; }
                                    if ($status === 'REJEITADA')  { $badgeClass = 'nd-badge-danger';  $icon = 'bi-x-lg'; }
                                    ?>
                                    <span class="nd-badge <?= $badgeClass ?>">
                                        <i class="bi <?= $icon ?> me-1"></i>
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>"
                                        class="nd-btn nd-btn-outline nd-btn-sm">
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