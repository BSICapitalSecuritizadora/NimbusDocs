<?php
/**
 * Espera em $viewData:
 * - array $documents
 * - string $csrfToken
 */
$documents = $viewData['documents'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['success'] ?? ($viewData['error'] ?? []);

// Filtro simples PHP-side
$search = $_GET['search'] ?? '';

if ($search) {
    $documents = array_filter($documents, function($d) use ($search) {
        $search = mb_strtolower($search);
        return str_contains(mb_strtolower($d['title'] ?? ''), $search) ||
               str_contains(mb_strtolower($d['user_full_name'] ?? ''), $search) ||
               str_contains(mb_strtolower($d['file_original_name'] ?? ''), $search);
    });
}
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-person-workspace text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Documentos de Clientes</h1>
            <p class="text-muted mb-0 small">Gestão do acervo digital individualizado</p>
        </div>
    </div>
    <a href="/admin/documents/new" class="nd-btn nd-btn-primary nd-btn-sm">
        <i class="bi bi-cloud-arrow-up me-1"></i>
        Novo Upload
    </a>
</div>

<!-- Alerts -->
<?php if (!empty($_SESSION['flash']['success'])): ?>
    <div class="nd-alert nd-alert-success mb-4" id="alertSuccess">
        <i class="bi bi-check-circle-fill text-success"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($_SESSION['flash']['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash']['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash']['error'])): ?>
    <div class="nd-alert nd-alert-danger mb-4" id="alertError">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($_SESSION['flash']['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php unset($_SESSION['flash']['error']); ?>
<?php endif; ?>

<!-- Documents List -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
         <form class="row g-3 align-items-center" method="get" action="/admin/documents">
            <div class="col-sm-8 col-md-10">
                <div class="nd-input-group">
                     <input type="text" name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                        class="nd-input"
                        placeholder="Buscar por cliente, título ou nome do arquivo..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-4 col-md-2">
                <button class="nd-btn nd-btn-primary w-100" type="submit">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
         <?php if (!$documents): ?>
            <div class="text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-folder-x text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhum documento encontrado</p>
                <p class="text-muted small mb-3">
                    <?= $search ? 'Tente ajustar sua busca.' : 'Realize o upload de documentos para os usuários.' ?>
                </p>
                 <?php if (!$search): ?>
                    <a href="/admin/documents/new" class="btn btn-sm btn-link text-decoration-none p-0">
                        Realizar primeiro upload
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Titular</th>
                            <th>Identificação</th>
                            <th>Arquivo Original</th>
                            <th>Data Cadastro</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $d): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="nd-avatar nd-avatar-sm nd-avatar-initials fw-bold" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
                                        <?= strtoupper(substr($d['user_full_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="d-flex flex-column" style="line-height:1.2;">
                                        <span class="fw-medium text-dark"><?= htmlspecialchars($d['user_full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($d['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                     <i class="bi bi-card-heading text-muted"></i>
                                     <span class="fw-bold text-dark"><?= htmlspecialchars($d['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </td>
                            <td>
                                <?php
                                $ext = strtolower(pathinfo($d['file_original_name'] ?? '', PATHINFO_EXTENSION));
                                $iconClass = match($ext) {
                                    'pdf' => 'bi-file-earmark-pdf-fill text-danger',
                                    'doc', 'docx' => 'bi-file-earmark-word-fill text-primary',
                                    'xls', 'xlsx', 'csv' => 'bi-file-earmark-excel-fill text-success',
                                    'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill text-info',
                                    default => 'bi-file-earmark-text-fill text-secondary'
                                };
                                ?>
                                <div class="d-flex align-items-center gap-2 small">
                                    <i class="bi <?= $iconClass ?> fs-6"></i>
                                    <div class="d-flex flex-column">
                                         <span class="text-dark font-monospace text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($d['file_original_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($d['file_original_name'] ?? 'arquivo', ENT_QUOTES, 'UTF-8') ?>
                                         </span>
                                         <span class="text-muted" style="font-size: 0.7rem;">
                                            <?php
                                            $bytes = (int)($d['file_size'] ?? 0);
                                            if ($bytes > 0) {
                                                if ($bytes < 1024) echo $bytes . ' B';
                                                elseif ($bytes < 1024 * 1024) echo number_format($bytes / 1024, 1, ',', '.') . ' KB';
                                                else echo number_format($bytes / (1024 * 1024), 2, ',', '.') . ' MB';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                         </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1 text-muted small">
                                    <i class="bi bi-calendar3"></i>
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($d['created_at'])) ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="/admin/documents/<?= (int)$d['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm" title="Visualizar Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        data-doc-id="<?= (int)$d['id'] ?>"
                                        data-doc-title="<?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?>"
                                        title="Remover Documento">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                 </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="nd-card-footer p-3 border-top text-end text-muted small">
                Total de <strong><?= count($documents) ?></strong> documentos encontrados
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 bg-danger-subtle text-danger">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trash-fill fs-5"></i>
                    <h5 class="modal-title fw-bold">Remover Documento</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="deleteForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-dark mb-0">
                        Tem certeza que deseja excluir o documento <strong id="deleteTitle" class="text-dark"></strong>?
                    </p>
                    <p class="text-muted small mt-2 mb-0">Esta ação é irreversível e removerá o arquivo do servidor.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-sm bg-danger text-white border-danger hover-danger-fill">
                        <i class="bi bi-trash me-1"></i> Confirmar Exclusão
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteTitle = document.getElementById('deleteTitle');

deleteModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const docId = btn.dataset.docId;
  const title = btn.dataset.docTitle;
  
  deleteTitle.textContent = title;
  deleteForm.action = `/admin/documents/${docId}/delete`;
});
</script>
