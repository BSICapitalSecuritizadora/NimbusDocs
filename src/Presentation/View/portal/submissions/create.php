<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */
$title = $old['title'] ?? '';
?>
<h1 class="h4 mb-3">Nova submissão</h1>

<form method="post" action="/portal/submissions">
    <input type="hidden" name="_token"
        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

    <div class="mb-3">
        <label class="form-label" for="title">Título</label>
        <input type="text" id="title" name="title"
            class="form-control"
            value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>