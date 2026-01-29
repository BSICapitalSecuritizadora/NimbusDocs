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
<!-- Header Section with Banner -->
<div class="nd-dashboard-banner d-block d-md-flex justify-content-between align-items-center mb-5 p-4 rounded-4 shadow-sm bg-white position-relative overflow-hidden">
    <div class="position-relative z-1">
        <h1 class="h3 fw-bold text-dark mb-1">
            Olá, <span style="color: var(--nd-primary);"><?= htmlspecialchars(explode(' ', $user['full_name'] ?? $user['email'])[0], ENT_QUOTES, 'UTF-8') ?></span>!
        </h1>
        <p class="text-secondary mb-0">
            Bem-vindo ao seu painel exclusivo de solicitações.
        </p>
    </div>
    
    <div class="position-relative z-1 mt-3 mt-md-0">
        <a href="/portal/submissions/new" class="nd-btn nd-btn-gold shadow-sm d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill hover-scale">
            <i class="bi bi-plus-lg fs-6"></i>
            <span class="fw-bold text-uppercase ls-1 fs-7">Nova Solicitação</span>
        </a>
    </div>

    <!-- Decorative overlapping circle -->
    <div class="position-absolute end-0 top-0 h-100 w-25 bg-primary opacity-10 rounded-start-pill d-none d-lg-block" style="transform: skewX(-20deg) translateX(50px);"></div>
</div>

<!-- Announcements -->
<?php if (!empty($announcements)): ?>
    <div class="mb-5 fade-in-up">
        <?php foreach ($announcements as $a): ?>
            <?php
            $level = $a['level'] ?? 'info';
            $class = 'bg-info-subtle text-info-emphasis border-info-subtle';
            $icon  = 'bi-info-circle-fill';
            
            if ($level === 'success') { $class = 'bg-success-subtle text-success-emphasis border-success-subtle'; $icon = 'bi-check-circle-fill'; }
            if ($level === 'warning') { $class = 'bg-warning-subtle text-warning-emphasis border-warning-subtle'; $icon = 'bi-exclamation-triangle-fill'; }
            if ($level === 'danger')  { $class = 'bg-danger-subtle text-danger-emphasis border-danger-subtle';  $icon = 'bi-x-circle-fill'; }
            ?>
            <div class="alert <?= $class ?> d-flex align-items-center shadow-sm border mb-3 rounded-3" role="alert">
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
        <div class="nd-card h-100 border-0 shadow-sm hover-lift transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-4 position-relative z-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1 mb-2">Total de Envios</div>
                        <div class="display-5 fw-bold text-dark"><?= $total ?></div>
                    </div>
                    <div class="rounded-4 bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center text-primary" style="width: 56px; height: 56px;">
                        <i class="bi bi-stack fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top border-light-subtle">
                     <span class="text-muted x-small">Histórico completo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Em Análise -->
    <div class="col-12 col-md-4">
        <div class="nd-card h-100 border-0 shadow-sm hover-lift transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-4 position-relative z-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1 mb-2">Em Análise</div>
                        <div class="display-5 fw-bold text-dark"><?= $pendentes ?></div>
                    </div>
                    <div class="rounded-4 bg-warning bg-opacity-10 p-3 d-flex align-items-center justify-content-center text-warning" style="width: 56px; height: 56px;">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                </div>
                 <div class="mt-3 pt-3 border-top border-light-subtle">
                     <span class="text-warning x-small fw-medium"><i class="bi bi-clock me-1"></i>Aguardando retorno</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Concluídos -->
    <div class="col-12 col-md-4">
        <div class="nd-card h-100 border-0 shadow-sm hover-lift transition-fast position-relative overflow-hidden">
            <div class="nd-card-body p-4 position-relative z-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold ls-1 mb-2">Concluídos</div>
                        <div class="display-5 fw-bold text-dark"><?= $concluidas ?></div>
                    </div>
                    <div class="rounded-4 bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center text-success" style="width: 56px; height: 56px;">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                    </div>
                </div>
                 <div class="mt-3 pt-3 border-top border-light-subtle">
                     <span class="text-success x-small fw-medium"><i class="bi bi-check-all me-1"></i>Processados com sucesso</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Submissions -->
<div class="nd-card border-0 shadow-sm mb-4">
    <div class="nd-card-header bg-white border-bottom py-4 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                <i class="bi bi-clock-history fs-5"></i>
            </div>
            <div>
                <h5 class="nd-card-title fw-bold text-dark mb-0">Envios Recentes</h5>
                <small class="text-muted">Acompanhe suas últimas solicitações</small>
            </div>
        </div>
        <?php if ($submissions): ?>
        <a href="/portal/submissions" class="btn btn-light btn-sm fw-bold text-primary hover-primary border rounded-pill px-3">
            Ver todos <i class="bi bi-arrow-right ms-1"></i>
        </a>
        <?php endif; ?>
    </div>
    
    <div class="nd-card-body p-0">
        <?php if (!$submissions): ?>
            <div class="text-center py-5 px-4">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-subtle border border-light shadow-sm position-relative" style="width: 100px; height: 100px;">
                        <i class="bi bi-inbox text-secondary opacity-25" style="font-size: 3rem;"></i>
                        <span class="position-absolute top-0 end-0 p-2 bg-warning border border-light rounded-circle"></span>
                    </div>
                </div>
                <h6 class="text-dark fw-bold mb-2 fs-5">Nenhum envio recente</h6>
                <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                    Você ainda não realizou nenhum envio de documentação. Clique no botão abaixo para iniciar uma nova solicitação.
                </p>
                <a href="/portal/submissions/new" class="nd-btn nd-btn-gold shadow px-4 py-2 rounded-3">
                    <i class="bi bi-plus-lg me-2"></i> Começar Agora
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
                            <th class="pe-4 py-3 text-end text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-white border shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                                <?= htmlspecialchars($s['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="x-small text-muted font-monospace">Protocolo: <?= $s['reference_code'] ?? $s['id'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-secondary small fw-medium">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <?= date('d/m/Y', strtotime($s['submitted_at'] ?? 'now')) ?>
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
                                        $badge = 'bg-warning-subtle text-warning-emphasis border-warning-subtle';
                                        $iconVal = 'bi-hourglass-split';
                                    } elseif (in_array($statusRaw, ['IN_REVIEW', 'UNDER_REVIEW', 'ANALISE'])) {
                                        $label = 'Em Análise';
                                        $badge = 'bg-info-subtle text-info-emphasis border-info-subtle';
                                        $iconVal = 'bi-search';
                                    } elseif (in_array($statusRaw, ['APPROVED', 'COMPLETED', 'CONCLUIDO', 'FINALIZADA'])) {
                                        $label = 'Concluído';
                                        $badge = 'bg-success-subtle text-success-emphasis border-success-subtle';
                                        $iconVal = 'bi-check-circle-fill';
                                    } elseif (in_array($statusRaw, ['REJECTED', 'REJEITADA'])) {
                                        $label = 'Rejeitado';
                                        $badge = 'bg-danger-subtle text-danger-emphasis border-danger-subtle';
                                        $iconVal = 'bi-x-circle-fill';
                                    }
                                    ?>
                                    <span class="badge rounded-pill border <?= $badge ?> px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold">
                                        <i class="bi <?= $iconVal ?>"></i> <?= $label ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>"
                                        class="btn btn-sm btn-light border text-muted hover-primary transition-fast shadow-sm rounded-pill px-3" 
                                        title="Ver detalhes">
                                        Detalhes <i class="bi bi-chevron-right ms-1"></i>
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

