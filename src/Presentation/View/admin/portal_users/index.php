<?php

/** @var array $items */
/** @var int $page */
/** @var int $totalPages */
/** @var string $search */

?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-people-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Usuários do Portal</h1>
            <p class="text-muted mb-0 small">Gerencie o acesso e cadastro de usuários externos</p>
        </div>
    </div>
    <a href="/admin/portal-users/create" class="nd-btn nd-btn-gold nd-btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Novo Usuário
    </a>
</div>

<!-- Filter & List Card -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
        <form class="row g-3 align-items-center" method="get" action="/admin/portal-users">
            <div class="col-sm-6 col-md-5">
                <div class="nd-input-group">
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                        class="nd-input"
                        placeholder="Buscar por nome, e-mail ou CPF..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <button class="nd-btn nd-btn-primary" type="submit">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
                <?php if (!empty($search)): ?>
                    <a href="/admin/portal-users" class="nd-btn nd-btn-outline ms-2">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$items): ?>
            <div class="text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-people text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhum usuário encontrado</p>
                <?php if (!empty($search)): ?>
                    <p class="text-muted small">Tente ajustar os termos da sua busca.</p>
                <?php else: ?>
                    <p class="text-muted small">Comece cadastrando um novo usuário no sistema.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>CPF</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nd-avatar nd-avatar-sm bg-light text-primary fw-bold border" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                            <?= strtoupper(substr($u['full_name'] ?? $u['name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-nowrap"><?= htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($u['document_number'])): ?>
                                        <div class="d-flex align-items-center text-dark">
                                            <i class="bi bi-card-heading me-2 text-muted small"></i>
                                            <span class="small font-monospace">
                                                <?php 
                                                    $doc = $u['document_number'];
                                                    $len = strlen($doc);
                                                    if ($len === 11) {
                                                        $doc = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
                                                    } elseif ($len === 14) {
                                                        $doc = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
                                                    }
                                                    echo htmlspecialchars($doc, ENT_QUOTES);
                                                ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match ($u['status'] ?? '') {
                                        'ACTIVE' => 'success',
                                        'INVITED' => 'info',
                                        'BLOCKED' => 'danger',
                                        'INACTIVE' => 'secondary',
                                        default => 'secondary'
                                    };
                                    $statusLabel = match ($u['status'] ?? '') {
                                        'ACTIVE' => 'Ativo',
                                        'INVITED' => 'Aguardando',
                                        'BLOCKED' => 'Suspenso',
                                        'INACTIVE' => 'Inativo',
                                        default => 'Inativo'
                                    };
                                    ?>
                                    <span class="nd-badge nd-badge-<?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/admin/portal-users/<?= (int)$u['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/portal-users/<?= (int)$u['id'] ?>/edit" class="nd-btn nd-btn-outline nd-btn-sm" title="Editar Usuário">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

<?php if ($totalPages > 1): ?>
                <?php
                    // Helper para manter query params
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    $buildUrl = function($p) use ($queryParams) {
                        return '?' . http_build_query(array_merge($queryParams, ['page' => $p]));
                    };

                    $start = ($page - 1) * $perPage + 1;
                    $end   = min($total, $page * $perPage);
                ?>
                <div class="nd-card-footer p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                         Exibindo <strong><?= $start ?></strong> a <strong><?= $end ?></strong> de <strong><?= number_format($total, 0, ',', '.') ?></strong> registros
                    </div>

                    <nav aria-label="Navegação de página">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page <= 1 ? '#' : $buildUrl($page - 1) ?>" tabindex="<?= $page <= 1 ? '-1' : '0' ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Numbers -->
                             <?php
                            $rangeStart = max(1, $page - 2);
                            $rangeEnd   = min($totalPages, $page + 2);
                            
                            if ($rangeEnd - $rangeStart < 4) {
                                if ($rangeStart == 1) {
                                    $rangeEnd = min($totalPages, $rangeStart + 4);
                                } elseif ($rangeEnd == $totalPages) {
                                    $rangeStart = max(1, $rangeEnd - 4);
                                }
                            }
                            ?>

                            <?php if ($rangeStart > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= $buildUrl(1) ?>">1</a>
                                </li>
                                <?php if ($rangeStart > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($p = $rangeStart; $p <= $rangeEnd; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $buildUrl($p) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($rangeEnd < $totalPages): ?>
                                <?php if ($rangeEnd < $totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= $buildUrl($totalPages) ?>"><?= $totalPages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page >= $totalPages ? '#' : $buildUrl($page + 1) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    @media (max-width: 575.98px) {
        .nd-page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .nd-page-header > .d-flex {
            width: 100%;
        }
        .nd-page-header .nd-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>