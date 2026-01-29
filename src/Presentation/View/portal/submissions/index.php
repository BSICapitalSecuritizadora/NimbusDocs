<?php
/** 
 * @var array{items: array, page: int, pages: int, total?: int} $pagination 
 * @var array{success?: ?string, warning?: ?string, error?: ?string} $flash 
 */

$items = $pagination['items'] ?? [];
$page  = $pagination['page'] ?? 1;
$pages = $pagination['pages'] ?? 1;
$success = $flash['success'] ?? null;
$error   = $flash['error']   ?? null;
?>
<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Meus Envios</h1>
        <p class="text-secondary mb-0">Gerencie todas as suas solicitações em um só lugar.</p>
    </div>
    <a href="/portal/submissions/create" class="nd-btn nd-btn-gold shadow-sm d-flex align-items-center gap-2 px-4 py-2 hover-scale rounded-3">
        <i class="bi bi-plus-lg fs-6"></i>
        <span>Novo Envio</span>
    </a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4 rounded-3" role="alert">
        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
        </div>
        <div class="flex-grow-1 fw-medium text-success-emphasis"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4 rounded-3" role="alert">
         <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
             <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
        </div>
        <div class="flex-grow-1 fw-medium text-danger-emphasis"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter Section -->
<div class="nd-card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
    <div class="nd-card-body p-4">
        <form action="" method="GET" role="search" aria-label="Filtros de submissões">
            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label for="searchQ" class="form-label text-secondary fw-bold x-small text-uppercase ls-1">Buscar</label>
                    <div class="nd-input-group position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="searchQ" name="q" class="form-control bg-light border-0 py-2 ps-5 rounded-3 fw-medium text-dark" 
                               placeholder="Protocolo, Assunto ou Empresa..." 
                               style="height: 48px;"
                               value="<?= htmlspecialchars($pagination['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label text-secondary fw-bold x-small text-uppercase ls-1">Situação</label>
                    <div class="position-relative">
                        <select class="form-select bg-light border-0 py-2 rounded-3 fw-medium text-secondary" 
                                id="filterStatus" name="status" style="height: 48px;">
                            <option value="">Todos os status</option>
                            <option value="APPROVED" <?= ($pagination['status'] ?? '') === 'APPROVED' ? 'selected' : '' ?>>Concluído</option>
                            <option value="UNDER_REVIEW" <?= ($pagination['status'] ?? '') === 'UNDER_REVIEW' ? 'selected' : '' ?>>Em Análise</option>
                            <option value="PENDING" <?= ($pagination['status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pendente</option>
                            <option value="REJECTED" <?= ($pagination['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>Rejeitado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-grid">
                        <button type="submit" class="nd-btn nd-btn-navy w-100 rounded-3 shadow-sm hover-translate" style="height: 48px; background-color: var(--nd-navy-900); color: white;">
                             Filtrar
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($pagination['search']) || !empty($pagination['status'])): ?>
                <div class="mt-3">
                    <a href="/portal/submissions" class="d-inline-flex align-items-center gap-1 text-decoration-none text-muted x-small uppercase fw-bold hover-danger transition-fast">
                        <i class="bi bi-x-lg"></i> Limpar filtros
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="nd-card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="nd-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white border-bottom">
                    <tr>
                        <th class="ps-4 py-4 text-uppercase text-secondary x-small fw-bold ls-1 border-0" style="width: 15%;">Protocolo</th>
                        <th class="py-4 text-uppercase text-secondary x-small fw-bold ls-1 border-0" style="width: 35%;">Assunto / Empresa</th>
                        <th class="py-4 text-uppercase text-secondary x-small fw-bold ls-1 border-0" style="width: 15%;">Situação</th>
                        <th class="py-4 text-uppercase text-secondary x-small fw-bold ls-1 border-0" style="width: 15%;">Data</th>
                        <th class="pe-4 py-4 text-end text-uppercase text-secondary x-small fw-bold ls-1 border-0" style="width: 20%;">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <div class="mb-4 d-inline-block rounded-circle bg-light p-4">
                                        <i class="bi bi-inbox fs-1 text-muted opacity-25"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Nenhum envio localizado</h6>
                                    <p class="text-secondary small mb-4">
                                        <?= (!empty($pagination['search']) || !empty($pagination['status'])) 
                                            ? 'Tente ajustar os filtros de busca.' 
                                            : 'Você ainda não enviou nenhum documento.' ?>
                                    </p>
                                    <?php if (empty($pagination['search']) && empty($pagination['status'])): ?>
                                        <a href="/portal/submissions/create" class="nd-btn nd-btn-navy px-4 py-2 rounded-3 shadow-sm" style="background-color: var(--nd-navy-900); color: white;">
                                            Começar agora
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $s): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <span class="badge bg-light text-dark border fw-bold font-monospace px-2 py-1 rounded">
                                        <?= htmlspecialchars($s['reference_code'] ?? $s['id'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark mb-1 text-truncate" style="max-width: 300px;">
                                            <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <?php if (!empty($s['company_name'])): ?>
                                            <span class="x-small text-muted text-uppercase fw-semibold ls-1">
                                                <?= htmlspecialchars($s['company_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <?php
                                    $statusRaw = strtoupper($s['status'] ?? '');
                                    $label = $statusRaw;
                                    $badge = 'bg-secondary-subtle text-secondary-emphasis';
                                    $iconVal = 'bi-circle';
                                    
                                    if (in_array($statusRaw, ['PENDING', 'PENDENTE'])) {
                                        $label = 'Pendente';
                                        $badge = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                        $iconVal = 'bi-hourglass-split';
                                    } elseif (in_array($statusRaw, ['IN_REVIEW', 'UNDER_REVIEW', 'ANALISE'])) {
                                        $label = 'Em Análise';
                                        $badge = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                        $iconVal = 'bi-search';
                                    } elseif (in_array($statusRaw, ['APPROVED', 'COMPLETED', 'CONCLUIDO', 'FINALIZADA'])) {
                                        $label = 'Concluído';
                                        $badge = 'bg-success-subtle text-success-emphasis border border-success-subtle';
                                        $iconVal = 'bi-check-circle-fill';
                                    } elseif (in_array($statusRaw, ['REJECTED', 'REJEITADA'])) {
                                        $label = 'Rejeitado';
                                        $badge = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
                                        $iconVal = 'bi-x-circle-fill';
                                    }
                                    ?>
                                    <span class="badge rounded-pill <?= $badge ?> px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold">
                                        <i class="bi <?= $iconVal ?>"></i> <?= $label ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex flex-column text-secondary small fw-medium">
                                        <span><?= date('d/m/Y', strtotime($s['submitted_at'] ?? 'now')) ?></span>
                                        <span class="text-muted x-small opacity-75"><?= date('H:i', strtotime($s['submitted_at'] ?? 'now')) ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <a href="/portal/submissions/<?= (int)$s['id'] ?>" 
                                       class="btn btn-light btn-sm fw-bold text-secondary hover-primary border transition-fast rounded-3 px-3">
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

<!-- Pagination -->
<?php if ($pages > 1): ?>
    <div class="mt-4 d-flex justify-content-center">
        <?php
        $baseUrl = '/portal/submissions';
        $queryParams = [
            'q'      => $pagination['search'] ?? null,
            'status' => $pagination['status'] ?? null
        ];
        include __DIR__ . '/../partials/pagination.php';
        ?>
    </div>
<?php endif; ?>