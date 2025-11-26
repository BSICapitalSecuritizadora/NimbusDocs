<?php
/**
 * Espera em $viewData:
 * - array $pagination { items, total, page, perPage, pages }
 * - array $filters { status?, portal_user_id? }
 * - array $flash { success?, error? }
 */

$pagination = $pagination ?? ['items'=>[], 'total'=>0, 'page'=>1, 'perPage'=>15, 'pages'=>1];
$filters    = $filters    ?? [];
$items      = $pagination['items'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Submissões do Portal</h1>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form class="row g-2 align-items-end mb-3" method="get" action="/admin/submissions">
  <div class="col-sm-3">
    <label class="form-label" for="f_status">Status</label>
    <select class="form-select" id="f_status" name="status">
      <?php $st = $filters['status'] ?? ''; ?>
      <option value="">Todos</option>
      <option value="PENDING" <?= $st==='PENDING'?'selected':''; ?>>Pendente</option>
      <option value="UNDER_REVIEW" <?= $st==='UNDER_REVIEW'?'selected':''; ?>>Em análise</option>
      <option value="COMPLETED" <?= $st==='COMPLETED'?'selected':''; ?>>Concluída</option>
      <option value="REJECTED" <?= $st==='REJECTED'?'selected':''; ?>>Rejeitada</option>
    </select>
  </div>
  <div class="col-sm-3">
    <label class="form-label" for="f_uid">ID Usuário</label>
    <input type="number" class="form-control" id="f_uid" name="portal_user_id" value="<?= htmlspecialchars((string)($filters['portal_user_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
  </div>
  <div class="col-sm-3">
    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
    <a class="btn btn-outline-secondary ms-1" href="/admin/submissions">Limpar</a>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Ref.</th>
        <th>Título</th>
        <th>Usuário</th>
        <th>Status</th>
        <th>Enviada em</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$items): ?>
        <tr>
          <td colspan="7" class="text-muted text-center">Nenhuma submissão encontrada.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($items as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><code><?= htmlspecialchars($row['reference_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></td>
            <td><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <?= htmlspecialchars($row['user_full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
              <small class="text-muted"><?= htmlspecialchars($row['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </td>
            <td><?= htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($row['submitted_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><a class="btn btn-sm btn-outline-primary" href="/admin/submissions/<?= (int)$row['id'] ?>">Ver</a></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (($pagination['pages'] ?? 1) > 1): ?>
<nav aria-label="Paginação">
  <ul class="pagination">
    <?php $page=(int)($pagination['page']??1); $pages=(int)($pagination['pages']??1); ?>
    <li class="page-item <?= $page<=1?'disabled':''; ?>">
      <a class="page-link" href="?page=<?= max(1,$page-1) ?>&status=<?= urlencode($filters['status']??'') ?>&portal_user_id=<?= urlencode($filters['portal_user_id']??'') ?>">Anterior</a>
    </li>
    <?php for($p=1;$p<=$pages;$p++): ?>
      <li class="page-item <?= $p===$page?'active':''; ?>">
        <a class="page-link" href="?page=<?= $p ?>&status=<?= urlencode($filters['status']??'') ?>&portal_user_id=<?= urlencode($filters['portal_user_id']??'') ?>"><?= $p ?></a>
      </li>
    <?php endfor; ?>
    <li class="page-item <?= $page>=$pages?'disabled':''; ?>">
      <a class="page-link" href="?page=<?= min($pages,$page+1) ?>&status=<?= urlencode($filters['status']??'') ?>&portal_user_id=<?= urlencode($filters['portal_user_id']??'') ?>">Próxima</a>
    </li>
  </ul>
</nav>
<?php endif; ?>
