<?php
/**
 * Espera em $viewData:
 * - array $documents
 * - string $csrfToken
 */
$documents = $documents ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Documentos do Portal</h1>
  <div>
    <a href="/admin/documents/new" class="btn btn-sm btn-primary">Novo documento</a>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Usuário</th>
        <th>Título</th>
        <th>Nome do arquivo</th>
        <th>Data</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$documents): ?>
      <tr><td colspan="6" class="text-center text-muted">Nenhum documento.</td></tr>
      <?php else: foreach ($documents as $d): ?>
      <tr>
        <td><?= (int)$d['id'] ?></td>
        <td>
          <?= htmlspecialchars($d['user_full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
          <small class="text-muted"><?= htmlspecialchars($d['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
        </td>
        <td><?= htmlspecialchars($d['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($d['file_original_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($d['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td class="text-end">
          <form method="post" action="/admin/documents/<?= (int)$d['id'] ?>/delete" onsubmit="return confirm('Excluir este documento?');">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button class="btn btn-sm btn-outline-danger">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
