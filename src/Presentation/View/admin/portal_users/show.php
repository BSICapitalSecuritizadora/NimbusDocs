<?php

/** @var array $user */
/** @var array $tokens */
/** @var string $csrfToken */
?>
<h1 class="h4 mb-3">Usuário do Portal</h1>

<div class="card mb-3">
    <div class="card-body">
        <p><strong>Nome:</strong> <?= htmlspecialchars($user['full_name'] ?? $user['name'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Link de acesso único</h2>

        <form method="post" action="/admin/portal-users/<?= (int)$user['id'] ?>/access-link">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary btn-sm">
                Gerar e enviar novo link
            </button>
        </form>

        <hr>

        <h3 class="h6">Histórico recente de links</h3>

        <?php if (!$tokens): ?>
            <p class="text-muted mb-0">Nenhum link gerado ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Criado em</th>
                            <th>Expira em</th>
                            <th>Usado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tokens as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($t['expires_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= $t['used_at']
                                        ? htmlspecialchars($t['used_at'], ENT_QUOTES, 'UTF-8')
                                        : '<span class="badge bg-warning text-dark">Ainda não usado</span>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>