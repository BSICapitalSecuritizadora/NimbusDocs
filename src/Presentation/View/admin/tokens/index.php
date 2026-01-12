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
            <div class="col-sm-6 col-md-4">
                <div class="nd-input-group">
                    <input type="text" name="search"
                        class="nd-input"
                        placeholder="Pesquisar por Titular ou E-mail..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <button type="submit" class="nd-btn nd-btn-primary w-100">
                    Filtrar Registros
                </button>
            </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$items): ?>
            <div class="text-center py-5">
                <i class="bi bi-qr-code-scan text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhuma credencial localizada.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Titular</th>
                            <th>Código da Credencial</th>
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
                                        <div class="nd-avatar" style="width: 32px; height: 32px; background: var(--nd-gray-100); color: var(--nd-gray-600); font-size: 0.75rem;">
                                            <?= strtoupper(substr($t['user_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-medium text-dark"><?= htmlspecialchars($t['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($t['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php $code = (string)($t['code'] ?? ''); ?>
                                    <?php if ($code !== ''): ?>
                                        <code class="px-2 py-1 rounded bg-light text-dark small border">
                                            <?= htmlspecialchars(substr($code, 0, 8) . '...', ENT_QUOTES, 'UTF-8') ?>
                                        </code>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-muted">
                                            <i class="bi bi-calendar-plus me-1"></i>
                                            <?= (new DateTime($t['created_at']))->format('d/m/Y H:i') ?>
                                        </span>
                                        <span class="<?= $isExpired ? 'text-danger' : 'text-dark' ?>">
                                            <i class="bi bi-calendar-x me-1"></i>
                                            <?= (new DateTime($t['expires_at']))->format('d/m/Y H:i') ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($isUsed): ?>
                                        <span class="nd-badge nd-badge-secondary">Utilizado</span>
                                    <?php elseif ($isExpired): ?>
                                        <span class="nd-badge nd-badge-danger">Expirado</span>
                                    <?php else: ?>
                                        <span class="nd-badge nd-badge-success">Ativo</span>
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