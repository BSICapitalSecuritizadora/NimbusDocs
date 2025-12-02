<?php
/**
 * Espera:
 * - array $users (id, full_name, email)
 * - string $csrfToken
 */
$users = $users ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Enviar documento</h1>
  <div>
    <a href="/admin/documents" class="btn btn-sm btn-outline-secondary">Voltar</a>
  </div>
</div>

<form action="/admin/documents" method="post" enctype="multipart/form-data" class="row gy-3">
  <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

  <div class="col-md-6">
    <label class="form-label">Usuário</label>
    <select name="portal_user_id" class="form-select" required>
      <option value="">Selecione…</option>
      <?php foreach ($users as $u): ?>
        <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['full_name'] ?? $u['email'] ?? ('#'.(int)$u['id']), ENT_QUOTES, 'UTF-8') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Título</label>
    <input type="text" name="title" class="form-control" required>
  </div>

  <div class="col-12">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control" rows="3"></textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Arquivo</label>
    <input type="file" name="file" class="form-control" required>
  </div>

  <div class="col-12">
    <button class="btn btn-primary">Enviar</button>
  </div>
</form>
