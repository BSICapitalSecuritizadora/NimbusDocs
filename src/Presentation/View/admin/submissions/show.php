<?php

/** @var array $submission */
/** @var array $userFiles */
/** @var array $responseFiles */
/** @var array $notes */
/** @var string $csrfToken */

?>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 mb-2">Arquivos enviados pelo usuário</h2>

        <?php if (!$userFiles): ?>
            <p class="text-muted mb-0">Nenhum arquivo anexado pelo usuário.</p>
        <?php else: ?>
            <ul class="mb-0">
                <?php foreach ($userFiles as $f): ?>
                    <li>
                        <?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="text-muted small">
                            (<?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB)
                        </span>
                        <a href="/admin/files/<?= (int)$f['id'] ?>/download"
                            class="ms-2 small">(baixar)</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 mb-2">Documentos enviados ao usuário</h2>

        <?php if (!$responseFiles): ?>
            <p class="text-muted">Nenhum documento de retorno enviado ainda.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($responseFiles as $f): ?>
                    <li>
                        <?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="text-muted small">
                            (<?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB)
                        </span>
                        <a href="/admin/files/<?= (int)$f['id'] ?>/download"
                            class="ms-2 small">(baixar como admin)</a>
                        <?php if ((int)$f['visible_to_user'] === 1): ?>
                            <span class="badge bg-success ms-1">Visível para o usuário</span>
                        <?php else: ?>
                            <span class="badge bg-secondary ms-1">Oculto</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <hr>

        <h3 class="h6 mb-2">Enviar novos documentos ao usuário</h3>
        <form method="post"
            action="/admin/submissions/<?= (int)$submission['id'] ?>/response-files"
            enctype="multipart/form-data">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-2">
                <input type="file" name="response_files[]" class="form-control form-control-sm" multiple>
                <div class="form-text">
                    Tipos comuns: PDF, DOCX, XLSX, ZIP, imagens. Tamanho máx. conforme configuração.
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                Enviar documentos
            </button>
        </form>
    </div>
</div>