<?php

use App\Support\StatusHelper;

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
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Meus Envios</h1>
            <p class="text-secondary mb-0">Gerencie todas as suas solicitações em um só lugar.</p>
        </div>
        <a href="/portal/submissions/create" class="nd-btn nd-btn-gold shadow-sm d-flex align-items-center gap-2 px-4 py-2">
            <i class="bi bi-plus-lg"></i>
            <span>Novo Envio</span>
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4" id="alertSuccess" role="alert">
            <i class="bi bi-check-circle-fill me-3 fs-5"></i>
            <div class="flex-grow-1"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4" id="alertError" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
            <div class="flex-grow-1"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="nd-card border-0 shadow-sm mb-4">
        <div class="nd-card-body p-4">
            <form action="" method="GET" role="search" aria-label="Filtros de submissões">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="searchQ" class="form-label small text-secondary fw-bold text-uppercase ls-1">Buscar</label>
                        <div class="nd-input-group">
                            <input type="text" id="searchQ" name="q" class="nd-input bg-light border-0 ps-5" 
                                   placeholder="Protocolo, Assunto ou Empresa..." 
                                   value="<?= htmlspecialchars($pagination['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-search nd-input-icon text-muted"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="filterStatus" class="form-label small text-secondary fw-bold text-uppercase ls-1">Situação</label>
                        <select class="form-select nd-input bg-light border-0" id="filterStatus" name="status">
                            <option value="">Todos os status</option>
                            <option value="PENDING" <?= ($pagination['status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pendente</option>
                            <option value="UNDER_REVIEW" <?= ($pagination['status'] ?? '') === 'UNDER_REVIEW' ? 'selected' : '' ?>>Em Análise</option>
                            <option value="APPROVED" <?= ($pagination['status'] ?? '') === 'APPROVED' ? 'selected' : '' ?>>Concluído</option>
                            <option value="REJECTED" <?= ($pagination['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>Rejeitado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="submit" class="nd-btn nd-btn-primary">
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>
                <?php if (!empty($pagination['search']) || !empty($pagination['status'])): ?>
                    <div class="mt-3">
                        <a href="/portal/submissions" class="d-inline-flex align-items-center gap-1 text-decoration-none text-muted small hover-danger transition-fast">
                            <i class="bi bi-x-circle"></i> Limpar filtros
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="nd-card border-0 shadow-sm">
        <div class="nd-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                     <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Protocolo</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Assunto / Empresa</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Situação</th>
                            <th class="py-3 text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Data</th>
                            <th class="pe-4 py-3 text-end text-uppercase text-muted x-small fw-bold ls-1 border-bottom-0">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$items): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="mb-3 rounded-circle bg-light p-4">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-25" aria-hidden="true"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Nenhum envio localizado</h6>
                                        <p class="text-secondary small mb-3">
                                            <?= (!empty($pagination['search']) || !empty($pagination['status'])) 
                                                ? 'Tente ajustar os filtros de busca.' 
                                                : 'Você ainda não enviou nenhum documento.' ?>
                                        </p>
                                        <?php if (empty($pagination['search']) && empty($pagination['status'])): ?>
                                            <a href="/portal/submissions/create" class="nd-btn nd-btn-sm nd-btn-primary">
                                                Começar agora
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $s): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="font-monospace fw-bold text-dark bg-light border px-2 py-1 rounded small user-select-all">
                                            <?= htmlspecialchars($s['reference_code'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                             <div class="rounded bg-light p-2 d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                                <i class="bi bi-file-text text-primary fs-5"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark text-truncate" style="max-width: 300px;">
                                                    <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <?php if (!empty($s['company_name'])): ?>
                                                    <span class="small text-muted text-uppercase x-small ls-1">
                                                        <?= htmlspecialchars($s['company_name'], ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        // TODO: Refactor StatusHelper to return new styles
                                        // For now, mapping manually for premium look
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
                                    <td class="text-secondary small">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar3"></i>
                                            <div>
                                                <div><?= date('d/m/Y', strtotime($s['submitted_at'] ?? 'now')) ?></div>
                                                <div class="x-small text-muted"><?= date('H:i', strtotime($s['submitted_at'] ?? 'now')) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/portal/submissions/<?= (int)$s['id'] ?>" 
                                           class="btn btn-sm btn-white border shadow-sm text-dark hover-primary transition-fast" 
                                           title="Ver detalhes">
                                            <span class="d-none d-md-inline me-1">Detalhes</span> <i class="bi bi-arrow-right"></i>
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
        <div class="mt-4">
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