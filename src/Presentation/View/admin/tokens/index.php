<?php

/** @var array $items */
/** @var int $page */
/** @var int $totalPages */
/** @var array $filters */
/** @var string $csrfToken */
$status = $filters['status'] ?? '';
$search = $filters['search'] ?? '';
?>
<h1 class="h4 mb-3">Tokens de acesso do portal</h1>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="get" action="/admin/tokens">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="valid" <?= $status === 'valid' ? 'selected' : '' ?>>Válidos</option>
                    <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Expirados</option>
                    <option value="used" <?= $status === 'used' ? 'selected' : '' ?>>Usados/Revogados</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label form-label-sm">Usuário (nome ou e-mail)</label>
                <input type="text" name="search"
                    class="form-control form-control-sm"
                    value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <?php if (!$items): ?>
            <p class="text-muted mb-0">Nenhum token encontrado com os filtros atuais.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuário</th>
                            <th>Token (parcial)</th>
                            <th>Criado em</th>
                            <th>Expira em</th>
                            <th>Status</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $t): ?>
                            <?php
                            $isUsed    = !empty($t['used_at']);
                            $isExpired = !$isUsed && (strtotime($t['expires_at']) < time());
                            ?>
                            <tr>
                                <td><?= (int)$t['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($t['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                                    <span class="text-muted small">
                                        <?= htmlspecialchars($t['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <?= htmlspecialchars(substr($t['token'], 0, 12) . '…', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td><?= htmlspecialchars($t['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($t['expires_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($isUsed): ?>
                                        <span class="badge bg-secondary">Usado/Revogado</span>
                                    <?php elseif ($isExpired): ?>
                                        <span class="badge bg-danger">Expirado</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Válido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="/admin/tokens/<?= (int)$t['id'] ?>"
                                        class="btn btn-sm btn-outline-secondary">Detalhes</a>

                                    <?php if (!$isUsed && !$isExpired): ?>
                                        <form method="post"
                                            action="/admin/tokens/<?= (int)$t['id'] ?>/revoke"
                                            class="d-inline">
                                            <input type="hidden" name="_token"
                                                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Revogar este token?')">
                                                Revogar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-2">
                    <ul class="pagination pagination-sm mb-0">
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
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>