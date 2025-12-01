<?php

/** @var array $items */
/** @var int $page */
/** @var int $totalPages */
/** @var string $search */
?>
<h1 class="h4 mb-3">Usuários do Portal</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex" method="get" action="/admin/portal-users">
        <input type="text" name="search"
            value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
            class="form-control form-control-sm me-2"
            placeholder="Nome ou e-mail">
        <button class="btn btn-sm btn-outline-secondary" type="submit">Buscar</button>
    </form>

    <a href="/admin/portal-users/create" class="btn btn-sm btn-primary">
        Novo usuário
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!$items): ?>
            <p class="text-muted mb-0">Nenhum usuário do portal encontrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Documento</th>
                            <th>Status</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($u['document'] ?? '-', ENT_QUOTES) ?></td>
                                <td>
                                    <?php if ((int)$u['is_active'] === 1): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="/admin/portal-users/<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        Detalhes
                                    </a>
                                    <a href="/admin/portal-users/<?= (int)$u['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                        Editar
                                    </a>
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
                                <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>">
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