<?php

/** @var array $pagination */
/** @var array $flash */

$items = $pagination['items'] ?? [];
$page  = $pagination['page'] ?? 1;
$pages = $pagination['pages'] ?? 1;
$success = $flash['success'] ?? null;
$error   = $flash['error']   ?? null;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold text-dark mb-0">Meus Envios</h1>
    <a href="/portal/submissions/create" class="nd-btn nd-btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Novo Envio
    </a>
</div>

<?php if ($success): ?>
    <div class="nd-alert nd-alert-success" id="alertSuccess">
        <i class="bi bi-check-circle-fill"></i>
        <span class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="nd-alert nd-alert-danger" id="alertError">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<!-- Search Form -->
<form action="" method="GET" class="mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" 
                       placeholder="Buscar por protocolo ou assunto..." 
                       value="<?= htmlspecialchars($pagination['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="nd-btn nd-btn-secondary w-100">
                Buscar
            </button>
        </div>
        <?php if (!empty($pagination['search'])): ?>
            <div class="col-md-2">
                <a href="/portal/submissions" class="nd-btn nd-btn-text text-muted w-100">
                    <i class="bi bi-x-lg me-1"></i> Limpar
                </a>
            </div>
        <?php endif; ?>
    </div>
</form>

<div class="nd-card">
    <div class="nd-card-body p-0">
        <div class="table-responsive">
            <table class="nd-table">
                <thead>
                    <tr>
                        <th class="ps-4">Protocolo</th>
                        <th>Assunto</th>
                        <th>Situação</th>
                        <th>Data de Envio</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="bi bi-inbox fs-1 opacity-25"></i>
                                </div>
                                <h6 class="fw-medium">Nenhum envio localizado</h6>
                                <p class="small mb-0">Você ainda não enviou nenhum documento.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-medium text-secondary bg-light rounded px-2 py-1 small">
                                        <?= htmlspecialchars($s['reference_code'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">
                                        <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusRaw = $s['status'] ?? '';
                                    $label = $statusRaw;
                                    $badgeClass = 'nd-badge-secondary';
                                    $icon = 'bi-circle';
                                    
                                    switch ($statusRaw) {
                                        case 'PENDING':
                                            $label = 'Pendente';
                                            $badgeClass = 'nd-badge-warning';
                                            $icon = 'bi-hourglass';
                                            break;
                                        case 'IN_REVIEW':
                                        case 'UNDER_REVIEW':
                                            $label = 'Em Análise';
                                            $badgeClass = 'nd-badge-info';
                                            $icon = 'bi-search';
                                            break;
                                        case 'APPROVED':
                                        case 'COMPLETED':
                                        case 'FINALIZADA':
                                            $label = 'Concluído';
                                            $badgeClass = 'nd-badge-success';
                                            $icon = 'bi-check2-circle';
                                            break;
                                        case 'REJECTED':
                                        case 'REJEITADA':
                                            $label = 'Rejeitado';
                                            $badgeClass = 'nd-badge-danger';
                                            $icon = 'bi-x-circle';
                                            break;
                                    }
                                    ?>
                                    <span class="nd-badge <?= $badgeClass ?>">
                                        <i class="bi <?= $icon ?> me-1"></i>
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-secondary">
                                        <i class="bi bi-calendar3 small"></i>
                                        <?= date('d/m/Y H:i', strtotime($s['submitted_at'] ?? 'now')) ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>" class="nd-btn nd-btn-sm nd-btn-outline" title="Ver detalhes">
                                        Detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pages > 1): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php
        $baseUrl = '/portal/submissions';
        $queryParams = [
            'q' => $pagination['search'] ?? null
        ];
        include __DIR__ . '/../partials/pagination.php';
        ?>
    </div>
<?php endif; ?>