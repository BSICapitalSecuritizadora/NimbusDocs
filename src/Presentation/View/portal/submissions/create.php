<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */

$title   = $old['title']   ?? '';
$message = $old['message'] ?? '';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <h1 class="h4 mb-3">Nova submissão</h1>

        <div class="card">
            <div class="card-body">
                <form method="post"
                    action="/portal/submissions"
                    enctype="multipart/form-data">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="form-label" for="title">Título</label>
                        <input type="text"
                            class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                            id="title" name="title" required
                            value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="message">Mensagem / descrição</label>
                        <textarea class="form-control"
                            id="message" name="message" rows="4"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="attachments">Anexos</label>
                        <input type="file"
                            class="form-control <?= isset($errors['attachments']) ? 'is-invalid' : '' ?>"
                            id="attachments"
                            name="attachments[]"
                            multiple>
                        <div class="form-text">
                            Tipos permitidos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV, JPG, PNG.<br>
                            Tamanho máximo: 100MB por arquivo.
                        </div>
                        <?php if (isset($errors['attachments'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['attachments'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/portal/submissions" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Enviar submissão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>