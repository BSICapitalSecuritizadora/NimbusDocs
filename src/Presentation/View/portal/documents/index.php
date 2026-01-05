<?php
/**
 * Espera:
 * - array $docs
 */
$docs = $docs ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Meus Documentos</h1>
</div>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>Título</th>
        <th>Descrição</th>
        <th>Data</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$docs): ?>
      <tr><td colspan="4" class="text-center text-muted">Nenhum documento disponível.</td></tr>
      <?php else: foreach ($docs as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($d['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($d['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="/portal/documents/<?= (int)$d['id'] ?>">Download</a></td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
