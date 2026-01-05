<?php

/** @var array $announcements */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Comunicados do Portal</h1>
        <p class="text-muted small mb-0">
            Mensagens institucionais exibidas aos usuários do portal NimbusDocs.
        </p>
    </div>
    <a href="/admin/announcements/new" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> Novo comunicado
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success py-2 small"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (!$announcements): ?>
            <p class="text-muted small mb-0">Nenhum comunicado cadastrado.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Nível</th>
                            <th>Período</th>
                            <th>Ativo</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $a): ?>
                            <tr>
                                <td>#<?= (int)$a['id'] ?></td>
                                <td><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php
                                    $level = $a['level'];
                                    $badge = 'bg-secondary';
                                    if ($level === 'info')    $badge = 'bg-info text-dark';
                                    if ($level === 'success') $badge = 'bg-success';
                                    if ($level === 'warning') $badge = 'bg-warning text-dark';
                                    if ($level === 'danger')  $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars($level, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?php if ($a['starts_at']): ?>
                                        <?= htmlspecialchars($a['starts_at'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                    <?php if ($a['ends_at']): ?>
                                        &rarr; <?= htmlspecialchars($a['ends_at'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int)$a['is_active'] === 1): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="/admin/announcements/<?= (int)$a['id'] ?>/edit"
                                        class="btn btn-sm btn-outline-secondary">
                                        Editar
                                    </a>

                                    <form method="post"
                                        action="/admin/announcements/<?= (int)$a['id'] ?>/delete"
                                        class="d-inline"
                                        onsubmit="return confirm('Remover este comunicado?');">
                                        <input type="hidden" name="_token"
                                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Excluir
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>