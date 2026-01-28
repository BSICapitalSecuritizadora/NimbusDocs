<?php
/**
 * Portal Dashboard
 * 
 * @var array $user
 * @var int $total
 * @var int $pendentes
 * @var int $concluidas
 * @var array $submissions
 * @var array $announcements
 */
?>
<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            Olá, <span style="color: var(--nd-primary);"><?= htmlspecialchars($user['full_name'] ?? $user['email'], ENT_QUOTES, 'UTF-8') ?></span>!
        </h1>
        <p class="text-secondary mb-0">
            Bem-vindo ao seu painel exclusivo de solicitações.
        </p>
    </div>
    
    <a href="/portal/submissions/new" class="nd-btn nd-btn-gold shadow-sm d-flex align-items-center gap-2 px-4 py-2">
        <i class="bi bi-plus-lg"></i>
        <span>Nova Solicitação</span>
    </a>
</div>

<!-- Announcements -->
<?php if (!empty($announcements)): ?>
    <div class="mb-5">
        <?php foreach ($announcements as $a): ?>
            <?php
            $level = $a['level'] ?? 'info';
            $class = 'bg-info-subtle text-info-emphasis border-info-subtle';
            $icon  = 'bi-info-circle-fill';
            
            if ($level === 'success') { $class = 'bg-success-subtle text-success-emphasis border-success-subtle'; $icon = 'bi-check-circle-fill'; }
            if ($level === 'warning') { $class = 'bg-warning-subtle text-warning-emphasis border-warning-subtle'; $icon = 'bi-exclamation-triangle-fill'; }
            if ($level === 'danger')  { $class = 'bg-danger-subtle text-danger-emphasis border-danger-subtle';  $icon = 'bi-x-circle-fill'; }
            ?>
            <div class="alert <?= $class ?> d-flex align-items-center shadow-sm border mb-3" role="alert">
                <i class="bi <?= $icon ?> fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">
                        <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h6>
                    <div class="small">
                        <?= nl2br(htmlspecialchars($a['body'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <!-- Total -->
    <div class="col-12 col-md-4">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Total de Envios</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $total ?></div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-layers-fill text-primary fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendentes -->
    <div class="col-12 col-md-4">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Em Análise</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $pendentes ?></div>
                    </div>
                    <div class="rounded-circle bg-warning-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Concluídos -->
    <div class="col-12 col-md-4">
        <div class="nd-card h-100 border-0 shadow-sm hover-shadow transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1">Concluídos</div>
                        <div class="h2 fw-bold mb-0 text-dark mt-2"><?= $concluidas ?></div>
                    </div>
                    <div class="rounded-circle bg-success-subtle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Submissions -->
<div class="nd-card border-0 shadow-sm mb-4">
    <div class="nd-card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-muted"></i>
            <h5 class="nd-card-title mb-0">Envios Recentes</h5>
        </div>
        <?php if ($submissions): ?>
        <a href="/portal/submissions" class="text-decoration-none small fw-bold text-primary">
            Ver todos <i class="bi bi-arrow-right"></i>
        </a>
        <?php endif; ?>
    </div>
    
    <div class="nd-card-body p-0">
        <?php if (!$submissions): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px;">
                        <i class="bi bi-inbox text-muted opacity-50 display-6"></i>
                    </div>
                </div>
                <h6 class="text-secondary fw-bold mb-1">Nenhum envio recente</h6>
                <p class="small text-muted mb-4">Seus envios aparecerão aqui.</p>
                <a href="/portal/submissions/new" class="nd-btn nd-btn-sm nd-btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Começar Agora
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Documento</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Enviado em</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Situação</th>
                            <th class="pe-4 py-3 text-end text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded bg-light p-2 d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                            <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark text-truncate" style="max-width: 250px;">
                                                <?= htmlspecialchars($s['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="small text-muted">ID: #<?= $s['id'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-secondary small">
                                        <i class="bi bi-calendar3"></i>
                                        <?= date('d/m/Y', strtotime($s['submitted_at'] ?? 'now')) ?>
                                        <span class="text-muted opacity-50">às <?= date('H:i', strtotime($s['submitted_at'] ?? 'now')) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $statusRaw = strtoupper($s['status'] ?? '');
                                    $label = $statusRaw;
                                    $badge = 'bg-secondary-subtle text-secondary-emphasis';
                                    $iconVal = 'bi-circle';
                                    
                                    if (in_array($statusRaw, ['PENDING', 'PENDENTE'])) {
                                        $label = 'Pendente';
                                        $badge = 'bg-warning-subtle text-warning-emphasis';
                                        $iconVal = 'bi-hourglass-split';
                                    } elseif (in_array($statusRaw, ['IN_REVIEW', 'UNDER_REVIEW', 'ANALISE'])) {
                                        $label = 'Em Análise';
                                        $badge = 'bg-info-subtle text-info-emphasis';
                                        $iconVal = 'bi-search';
                                    } elseif (in_array($statusRaw, ['APPROVED', 'COMPLETED', 'CONCLUIDO', 'FINALIZADA'])) {
                                        $label = 'Concluído';
                                        $badge = 'bg-success-subtle text-success-emphasis';
                                        $iconVal = 'bi-check-circle-fill';
                                    } elseif (in_array($statusRaw, ['REJECTED', 'REJEITADA'])) {
                                        $label = 'Rejeitado';
                                        $badge = 'bg-danger-subtle text-danger-emphasis';
                                        $iconVal = 'bi-x-circle-fill';
                                    }
                                    ?>
                                    <span class="badge rounded-pill border <?= $badge ?> px-3 py-2 d-inline-flex align-items-center gap-1 fw-medium">
                                        <i class="bi <?= $iconVal ?>"></i> <?= $label ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>"
                                        class="btn btn-sm btn-light text-muted hover-primary border transition-fast" 
                                        title="Ver detalhes">
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

<div class="text-center mt-5 mb-4">
    <p class="small text-muted">
        <i class="bi bi-shield-lock me-1"></i> Ambiente seguro e monitorado.
    </p>
</div>