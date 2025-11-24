<?php

/** @var array $submission */
/** @var array $files */
?>
<div class="mb-3">
    <a href="/admin/submissions" class="btn btn-outline-secondary btn-sm">
        &larr; Voltar para a lista
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h1 class="h5 mb-3">
            <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?>
        </h1>

        <dl class="row mb-0">
            <dt class="col-sm-3">Código</dt>
            <dd class="col-sm-9"><code><?= htmlspecialchars($submission['reference_code'], ENT_QUOTES, 'UTF-8') ?></code></dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($submission['status'], ENT_QUOTES, 'UTF-8') ?></dd>

            <dt class="col-sm-3">Enviado em</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($submission['submitted_at'], ENT_QUOTES, 'UTF-8') ?></dd>
        </dl>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 mb-2">Usuário final</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nome</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($submission['user_full_name'], ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">E-mail</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($submission['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">Documento</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($submission['user_document_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">Telefone</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($submission['user_phone_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 mb-2">Mensagem</h2>
                <?php if (!empty($submission['message'])): ?>
                    <p class="mb-0">
                        <?= nl2br(htmlspecialchars($submission['message'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhuma mensagem informada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h6 mb-2">Anexos</h2>
        <?php if (!$files): ?>
            <p class="text-muted mb-0">
                Nenhum arquivo enviado para esta submissão.
            </p>
        <?php else: ?>
            <ul class="mb-0">
                <?php foreach ($files as $f): ?>
                    <li>
                        <?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?>
                        <!-- Link de download/visualização entra na fase de upload -->
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>