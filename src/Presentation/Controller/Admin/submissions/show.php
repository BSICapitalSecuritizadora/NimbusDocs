<?php

/** @var array $submission */
/** @var array $files */
/** @var array $notes */
/** @var string $csrfToken */

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

    <div class="row mt-3">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Atualizar status</h2>

                    <form method="post" action="/admin/submissions/<?= (int) $submission['id'] ?>/status">
                        <input type="hidden" name="_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <?php
                                $currentStatus = $submission['status'];
$options = [
    'PENDING' => 'Pendente',
    'UNDER_REVIEW' => 'Em análise',
    'COMPLETED' => 'Concluído',
    'REJECTED' => 'Rejeitado',
];
foreach ($options as $value => $label):
    ?>
                                    <option value="<?= $value ?>" <?= $currentStatus === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="note">Comentário</label>
                            <textarea class="form-control" id="note" name="note" rows="3"
                                placeholder="Comentário sobre esta alteração (opcional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Visibilidade do comentário</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    name="visibility" id="vis_user" value="USER_VISIBLE" checked>
                                <label class="form-check-label" for="vis_user">
                                    Visível para o usuário final
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    name="visibility" id="vis_admin" value="ADMIN_ONLY">
                                <label class="form-check-label" for="vis_admin">
                                    Apenas para administradores
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            Salvar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Histórico / notas</h2>

                    <?php if (!$notes): ?>
                        <p class="text-muted mb-0">
                            Nenhuma nota registrada para esta submissão.
                        </p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($notes as $n): ?>
                                <li class="mb-2">
                                    <div class="small text-muted">
                                        <?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                        &mdash;
                                        <?= $n['visibility'] === 'ADMIN_ONLY' ? 'Interna' : 'Visível ao usuário' ?>
                                    </div>
                                    <div>
                                        <?= nl2br(htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8')) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>