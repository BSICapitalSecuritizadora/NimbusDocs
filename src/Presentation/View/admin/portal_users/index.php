<?php

/** @var array $items */
/** @var int $page */
/** @var int $totalPages */
/** @var string $search */

?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-people-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Usuários Cadastrados</h1>
            <p class="text-muted mb-0 small">Controle de acesso e cadastro de usuários externos</p>
        </div>
    </div>
    <a href="/admin/portal-users/create" class="nd-btn nd-btn-gold nd-btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Novo Cadastro
    </a>
</div>

<!-- Filter & List Card -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
        <form class="row g-3 align-items-center" method="get" action="/admin/portal-users">
            <div class="col-sm-6 col-md-4">
                <div class="nd-input-group">
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                        class="nd-input"
                        placeholder="Pesquisar por Usuário ou E-mail..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <button class="nd-btn nd-btn-primary" type="submit">
                    Filtrar Registros
                </button>
                <?php if (!empty($search)): ?>
                    <a href="/admin/portal-users" class="nd-btn nd-btn-outline ms-2">Limpar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$items): ?>
            <div class="text-center py-5">
                <i class="bi bi-people text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum usuário localizado.</p>
                <?php if (!empty($search)): ?>
                    <p class="text-muted small mt-1">Refine os termos da sua busca.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Identificação do Usuário</th>
                            <th>CPF</th>
                            <th>Situação Cadastral</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nd-avatar" style="width: 36px; height: 36px; background: var(--nd-gray-100); color: var(--nd-gray-600); font-size: 0.8rem;">
                                            <?= strtoupper(substr($u['full_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-medium text-dark"><?= htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($u['document_number'])): ?>
                                        <code class="px-2 py-1 rounded bg-light text-dark small border">
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
                                        </code>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match ($u['status'] ?? '') {
                                        'ACTIVE' => 'success',
                                        'INVITED' => 'info',
                                        'BLOCKED' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusLabel = match ($u['status'] ?? '') {
                                        'ACTIVE' => 'Ativo',
                                        'INVITED' => 'Aguardando Cadastro',
                                        'BLOCKED' => 'Suspenso',
                                        default => 'Inativo'
                                    };
                                    ?>
                                    <span class="nd-badge nd-badge-<?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="/admin/portal-users/<?= (int)$u['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm" title="Ficha Técnica">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/portal-users/<?= (int)$u['id'] ?>/edit" class="nd-btn nd-btn-outline nd-btn-sm" title="Gerenciar Cadastro">
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
                <div class="nd-card-footer p-3 border-top">
                    <nav aria-label="Paginação">
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>