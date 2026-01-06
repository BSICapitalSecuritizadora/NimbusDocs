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

$query = http_build_query([
    'status'         => $filters['status'] ?? '',
    'portal_user_id' => $filters['portal_user_id'] ?? '',
]);
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div class="d-flex align-items-center gap-3">
    <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
        <i class="bi bi-inbox-fill text-white"></i>
    </div>
    <div>
        <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Submissões do Portal</h1>
        <p class="text-muted mb-0 small">Gerencie os envios recebidos</p>
    </div>
  </div>
  <a href="/admin/submissions/export/csv?<?= $query ?>" class="nd-btn nd-btn-outline nd-btn-sm">
    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Exportar CSV
  </a>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="nd-alert nd-alert-success mb-4">
    <i class="bi bi-check-circle-fill text-success"></i>
    <span class="nd-alert-text"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?></span>
  </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
  <div class="nd-alert nd-alert-danger mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span class="nd-alert-text"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></span>
  </div>
<?php endif; ?>

<!-- Filter & List Card -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
        <form class="row g-3 align-items-end" method="get" action="/admin/submissions">
          <div class="col-sm-3">
            <label class="nd-label" for="f_status">Status</label>
            <div class="nd-input-group">
                <select class="nd-input" id="f_status" name="status" style="padding-left: 1rem;">
                  <?php $st = $filters['status'] ?? ''; ?>
                  <option value="">Todos os status</option>
                  <option value="PENDING" <?= $st==='PENDING'?'selected':''; ?>>Pendente</option>
                  <option value="UNDER_REVIEW" <?= $st==='UNDER_REVIEW'?'selected':''; ?>>Em análise</option>
                  <option value="COMPLETED" <?= $st==='COMPLETED'?'selected':''; ?>>Concluída</option>
                  <option value="REJECTED" <?= $st==='REJECTED'?'selected':''; ?>>Rejeitada</option>
                </select>
            </div>
          </div>
          <div class="col-sm-3">
            <label class="nd-label" for="f_uid">ID Usuário</label>
            <input type="number" class="nd-input" id="f_uid" name="portal_user_id" placeholder="Ex: 123" value="<?= htmlspecialchars((string)($filters['portal_user_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-sm-4">
            <div class="d-flex gap-2">
                <button type="submit" class="nd-btn nd-btn-primary">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                <a class="nd-btn nd-btn-outline" href="/admin/submissions">Limpar</a>
            </div>
          </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <div class="table-responsive">
          <table class="nd-table">
            <thead>
              <tr>
                <th style="width: 120px;">Ref.</th>
                <th>Título</th>
                <th>Usuário</th>
                <th style="width: 140px;">Status</th>
                <th>Data envio</th>
                <th style="width: 100px;"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$items): ?>
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                        <span class="text-muted">Nenhuma submissão encontrada.</span>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($items as $row): ?>
                    <?php
                    $statusClass = match($row['status'] ?? '') {
                        'PENDING', 'UNDER_REVIEW' => 'warning',
                        'COMPLETED' => 'success',
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'secondary'
                    };
                    $statusLabel = match($row['status'] ?? '') {
                        'PENDING' => 'Pendente',
                        'UNDER_REVIEW' => 'Em Análise',
                        'COMPLETED' => 'Concluída',
                        'APPROVED' => 'Aprovada',
                        'REJECTED' => 'Rejeitada',
                        default => $row['status'] ?? '-'
                    };
                    ?>
                  <tr>
                    <td><code class="px-2 py-1 rounded bg-light text-dark small border"><?= htmlspecialchars($row['reference_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td class="fw-medium text-dark"><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                          <div class="nd-avatar" style="width: 24px; height: 24px; font-size: 0.7rem;">
                              <?= strtoupper(substr($row['user_full_name'] ?? 'U', 0, 1)) ?>
                          </div>
                          <div class="d-flex flex-column" style="line-height: 1.2;">
                              <span><?= htmlspecialchars($row['user_full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                              <small class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($row['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                          </div>
                      </div>
                    </td>
                    <td>
                        <span class="nd-badge nd-badge-<?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                      <?php
                        $submittedAt = $row['submitted_at'] ?? '';
                        if ($submittedAt) {
                            try {
                                $date = new DateTime($submittedAt);
                                $formattedDate = $date->format('d/m/Y H:i');
                            } catch (Exception $e) {
                                $formattedDate = $submittedAt;
                            }
                        } else {
                            $formattedDate = '';
                        }
                        echo htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8');
                      ?>
                    </td>
                    <td class="text-end">
                        <a class="nd-btn nd-btn-outline nd-btn-sm" href="/admin/submissions/<?= (int)$row['id'] ?>">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if (($pagination['pages'] ?? 1) > 1): ?>
    <div class="nd-card-footer p-3 border-top">
        <nav aria-label="Paginação">
          <ul class="pagination pagination-sm justify-content-end mb-0">
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
    </div>
    <?php endif; ?>
</div>
