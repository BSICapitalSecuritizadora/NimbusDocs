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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Meus Envios</h1>
            <p class="text-secondary mb-0 small">Gerencie suas submissões e acompanhe o status.</p>
        </div>
        <a href="/portal/submissions/create" class="nd-btn nd-btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Novo Envio
        </a>
    </div>

    <?php if ($success): ?>
        <div class="nd-alert nd-alert-success mb-4" id="alertSuccess" role="alert" aria-live="polite">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
            <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()" aria-label="Fechar alerta">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="nd-alert nd-alert-danger mb-4" id="alertError" role="alert" aria-live="assertive">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()" aria-label="Fechar alerta">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="nd-filter-card">
        <form action="" method="GET" role="search" aria-label="Filtros de submissões">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="searchQ" class="form-label small text-secondary fw-medium">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </span>
                        <input type="text" id="searchQ" name="q" class="form-control border-start-0 ps-0 shadow-none" 
                               placeholder="Protocolo, Assunto ou Empresa..." 
                               value="<?= htmlspecialchars($pagination['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="filterStatus" class="form-label small text-secondary fw-medium">Situação</label>
                    <select class="form-select shadow-none" id="filterStatus" name="status">
                        <option value="">Todos</option>
                        <option value="PENDING" <?= ($pagination['status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pendente</option>
                        <option value="UNDER_REVIEW" <?= ($pagination['status'] ?? '') === 'UNDER_REVIEW' ? 'selected' : '' ?>>Em Análise</option>
                        <option value="APPROVED" <?= ($pagination['status'] ?? '') === 'APPROVED' ? 'selected' : '' ?>>Concluído</option>
                        <option value="REJECTED" <?= ($pagination['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>Rejeitado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid w-100 gap-2">
                        <button type="submit" class="nd-btn nd-btn-secondary">
                            Filtrar
                        </button>
                        <?php if (!empty($pagination['search']) || !empty($pagination['status'])): ?>
                            <a href="/portal/submissions" class="btn btn-link btn-sm text-decoration-none text-muted p-0 text-center">
                                Limpar filtros
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="nd-card">
        <div class="nd-card-body p-0">
            <div class="table-responsive">
                <table class="nd-table nd-table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-secondary small fw-bold" scope="col">Protocolo</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold" scope="col">Assunto / Empresa</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold" scope="col">Situação</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold" scope="col">Data</th>
                            <th class="pe-4 py-3 text-end text-uppercase text-secondary small fw-bold" scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$items): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="mb-3 rounded-circle bg-light p-4">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50" aria-hidden="true"></i>
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
                                        <span class="font-monospace fw-bold text-dark bg-secondary-subtle px-2 py-1 rounded small">
                                            <?= htmlspecialchars($s['reference_code'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark">
                                                <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <?php if (!empty($s['company_name'])): ?>
                                                <span class="small text-secondary">
                                                    <?= htmlspecialchars($s['company_name'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php $statusInfo = StatusHelper::translate($s['status'] ?? ''); ?>
                                        <span class="nd-badge <?= $statusInfo['badge'] ?>">
                                            <i class="bi <?= $statusInfo['icon'] ?> me-1" aria-hidden="true"></i>
                                            <?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= date('d/m/Y', strtotime($s['submitted_at'] ?? 'now')) ?>
                                        <span class="d-block text-muted" style="font-size: 0.75rem;">
                                            <?= date('H:i', strtotime($s['submitted_at'] ?? 'now')) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/portal/submissions/<?= (int)$s['id'] ?>" class="nd-btn nd-btn-sm nd-btn-text text-primary" title="Ver detalhes">
                                            Detalhes <i class="bi bi-arrow-right ms-1"></i>
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