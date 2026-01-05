<?php

/** @var array $data */
/** @var string $csrfToken */
/** @var string $mode */

$isEdit = ($mode === 'edit');
$action = $isEdit
    ? "/admin/announcements/{$data['id']}"
    : "/admin/announcements";

$title = $isEdit ? 'Editar comunicado' : 'Novo comunicado';

$startsDate = $data['starts_at'] ? substr($data['starts_at'], 0, 10) : '';
$endsDate   = $data['ends_at']   ? substr($data['ends_at'],   0, 10) : '';
?>
<h1 class="h4 mb-3"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text"
                    name="title"
                    class="form-control form-control-sm"
                    required
                    value="<?= htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Mensagem</label>
                <textarea name="body"
                    rows="4"
                    class="form-control form-control-sm"
                    required><?= htmlspecialchars($data['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Nível</label>
                <select name="level" class="form-select form-select-sm">
                    <?php
                    $levels = ['info' => 'Informativo', 'success' => 'Sucesso', 'warning' => 'Alerta', 'danger' => 'Crítico'];
                    foreach ($levels as $key => $label):
                        $selected = (($data['level'] ?? 'info') === $key) ? 'selected' : '';
                    ?>
                        <option value="<?= $key ?>" <?= $selected ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Início (opcional)</label>
                    <input type="date"
                        name="starts_at"
                        class="form-control form-control-sm"
                        value="<?= htmlspecialchars($startsDate, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fim (opcional)</label>
                    <input type="date"
                        name="ends_at"
                        class="form-control form-control-sm"
                        value="<?= htmlspecialchars($endsDate, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            <?= ((int)($data['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Ativo
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="/admin/announcements" class="btn btn-link btn-sm me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>