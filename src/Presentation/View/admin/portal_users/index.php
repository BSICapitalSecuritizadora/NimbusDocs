<?php

/** @var array $pagination */
/** @var string $csrfToken */
/** @var array $flash */

$items = $pagination['items'] ?? [];
$page  = $pagination['page'] ?? 1;
$pages = $pagination['pages'] ?? 1;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Usuários Finais</h1>
    <a href="/admin/portal-users/create" class="btn btn-primary btn-sm">Novo usuário</a>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success py-2">
        <?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-danger py-2">
        <?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>CPF</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                Nenhum usuário cadastrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $user): ?>
                            <tr>
                                <td><?= (int)$user['id'] ?></td>
                                <td><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['document_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-end">
                                    <a href="/admin/portal-users/<?= (int)$user['id'] ?>/edit"
                                        class="btn btn-sm btn-outline-secondary">
                                        Editar
                                    </a>

                                    <?php if ($user['status'] === 'ACTIVE' || $user['status'] === 'INVITED'): ?>
                                        <form method="post"
                                            action="/admin/portal-users/<?= (int)$user['id'] ?>/deactivate"
                                            class="d-inline"
                                            onsubmit="return confirm('Deseja desativar este usuário?');">
                                            <input type="hidden" name="_token"
                                                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Desativar
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
    <nav class="mt-3">
        <ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $pages; $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="/admin/portal-users?page=<?= $p ?>">
                        <?= $p ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>