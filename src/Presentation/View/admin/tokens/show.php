<?php

/** @var array $token */
$isUsed    = !empty($token['used_at']);
$isExpired = !$isUsed && (strtotime($token['expires_at']) < time());
?>
<h1 class="h4 mb-3">Detalhes do token #<?= (int)$token['id'] ?></h1>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="mb-3">Informações gerais</h5>

        <dl class="row mb-0">
            <dt class="col-sm-3">Usuário do portal</dt>
            <dd class="col-sm-9">
                <?= htmlspecialchars($token['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                <span class="text-muted small">
                    <?= htmlspecialchars($token['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </dd>

            <dt class="col-sm-3">Token</dt>
            <dd class="col-sm-9">
                <code class="small"><?= htmlspecialchars($token['token'], ENT_QUOTES, 'UTF-8') ?></code>
            </dd>

            <dt class="col-sm-3">Criado em</dt>
            <dd class="col-sm-9">
                <?= htmlspecialchars($token['created_at'], ENT_QUOTES, 'UTF-8') ?>
            </dd>

            <dt class="col-sm-3">Expira em</dt>
            <dd class="col-sm-9">
                <?= htmlspecialchars($token['expires_at'], ENT_QUOTES, 'UTF-8') ?>
            </dd>

            <dt class="col-sm-3">Usado em</dt>
            <dd class="col-sm-9">
                <?= $token['used_at']
                    ? htmlspecialchars($token['used_at'], ENT_QUOTES, 'UTF-8')
                    : '<span class="text-muted">Ainda não usado</span>' ?>
            </dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                <?php if ($isUsed): ?>
                    <span class="badge bg-secondary">Usado/Revogado</span>
                <?php elseif ($isExpired): ?>
                    <span class="badge bg-danger">Expirado</span>
                <?php else: ?>
                    <span class="badge bg-success">Válido</span>
                <?php endif; ?>
            </dd>
        </dl>
    </div>
</div>

<a href="/admin/tokens" class="btn btn-sm btn-outline-secondary">Voltar</a>
<?php if (!$isUsed && !$isExpired): ?>
    <!-- Se quiser, aqui poderia ter botão de revogar também -->
<?php endif; ?>