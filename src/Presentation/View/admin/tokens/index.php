<?php

/** @var array $items */
/** @var int $page */
/** @var int $totalPages */
/** @var array $filters */
/** @var string $csrfToken */
$status = $filters['status'] ?? '';
$search = $filters['search'] ?? '';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-key-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Histórico de Credenciais</h1>
            <p class="text-muted mb-0 small">Monitoramento de links de autenticação temporária</p>
        </div>
    </div>
</div>

<!-- Filters & List Card -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
        <form class="row g-3 align-items-center" method="get" action="/admin/tokens">
            <div class="col-sm-6 col-md-3">
                <div class="nd-input-group">
                    <select name="status" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todas as situações</option>
                        <option value="valid" <?= $status === 'valid' ? 'selected' : '' ?>>Válidos</option>
                        <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Expirados</option>
                        <option value="used" <?= $status === 'used' ? 'selected' : '' ?>>Utilizados/Revogados</option>
                    </select>
                    <i class="bi bi-filter nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-5">
                <div class="nd-input-group">
                    <input type="text" name="search"
                        class="nd-input"
                        placeholder="Buscar por usuário ou e-mail..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <button type="submit" class="nd-btn nd-btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
            </div>
            <?php if (!empty($search) || !empty($status)): ?>
                <div class="col-sm-6 col-md-2">
                     <a href="/admin/tokens" class="nd-btn nd-btn-outline w-100">
                        <i class="bi bi-x-lg me-1"></i> Limpar
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$items): ?>
            <div class="text-center py-5">
                 <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-qr-code-scan text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhuma credencial localizada</p>
                <p class="text-muted small">Tente ajustar os filtros da sua busca.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Titular</th>
                            <th>Código</th>
                            <th>Vigência</th>
                            <th>Situação</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $t): ?>
                            <?php
                            $isUsed    = !empty($t['used_at']);
                            $isExpired = !$isUsed && (strtotime($t['expires_at']) < time());
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nd-avatar nd-avatar-sm bg-light text-primary fw-bold border" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                              <?= strtoupper(substr($t['user_name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($t['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($t['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php $code = (string)($t['code'] ?? ''); ?>
                                    <?php if ($code !== ''): ?>
                                        <code class="px-2 py-1 rounded bg-light text-dark small border font-monospace">
                                            <?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>
                                        </code>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-muted d-flex align-items-center gap-1">
                                            <i class="bi bi-calendar-plus"></i>
                                            <?= (new DateTime($t['created_at']))->format('d/m/Y H:i') ?>
                                        </span>
                                        <span class="<?= $isExpired ? 'text-danger' : 'text-dark' ?> d-flex align-items-center gap-1">
                                            <i class="bi bi-calendar-x"></i>
                                            <?= (new DateTime($t['expires_at']))->format('d/m/Y H:i') ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($isUsed): ?>
                                         <div class="d-flex align-items-center gap-1 text-success small fw-medium">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>Utilizado</span>
                                        </div>
                                    <?php elseif ($isExpired): ?>
                                        <span class="nd-badge nd-badge-secondary">Expirado</span>
                                    <?php else: ?>
                                        <span class="nd-badge nd-badge-warning">Válido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="/admin/tokens/<?= (int)$t['id'] ?>"
                                            class="nd-btn nd-btn-outline nd-btn-sm" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <?php if (!$isUsed && !$isExpired): ?>
                                            <form method="post"
                                                action="/admin/tokens/<?= (int)$t['id'] ?>/revoke"
                                                class="d-inline"
                                                onsubmit="return confirm('Deseja realmente revogar esta credencial?');">
                                                <input type="hidden" name="_token"
                                                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit"
                                                    class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" title="Revogar Credencial">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="nd-card-footer p-3 border-top">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?page=<?= $p ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>">
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