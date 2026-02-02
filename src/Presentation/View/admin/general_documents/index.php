<?php
/**
 * Espera em $viewData:
 * - array $documents
 * - array $categories
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - array $old (dados antigos do formulário)
 */
$documents = $viewData['documents'] ?? [];
$categories = $viewData['categories'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];

// Filtros Simples (PHP-side, já que o controller retorna tudo)
$search = $_GET['search'] ?? '';
$catFilter = $_GET['category_id'] ?? '';

if ($search) {
    $documents = array_filter($documents, function($d) use ($search) {
        return stripos($d['title'], $search) !== false || stripos($d['file_original_name'], $search) !== false;
    });
}
if ($catFilter) {
    $documents = array_filter($documents, function($d) use ($catFilter) {
        return (int)$d['category_id'] === (int)$catFilter;
    });
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-file-earmark-text-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Biblioteca Digital</h1>
            <p class="text-muted mb-0 small">Repositório de documentos e normativas institucionais</p>
        </div>
    </div>
    <button class="nd-btn nd-btn-gold nd-btn-sm" data-bs-toggle="modal" data-bs-target="#createDocumentModal">
        <i class="bi bi-cloud-arrow-up-fill me-1"></i>
        Nova Publicação
    </button>
</div>

<!-- Alerts -->
<?php if (!empty($flash['success'])): ?>
    <div class="nd-alert nd-alert-success mb-4" id="alertSuccess">
        <i class="bi bi-check-circle-fill text-success"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
    <div class="nd-alert nd-alert-danger mb-4" id="alertError">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<!-- Documents List -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
         <form class="row g-3 align-items-center" method="get" action="/admin/general-documents">
            <div class="col-sm-6 col-md-3">
                <div class="nd-input-group">
                    <select name="category_id" class="nd-input form-select" style="padding-left: 2.5rem;">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (int)$catFilter === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="bi bi-folder nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="nd-input-group">
                     <input type="text" name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                        class="nd-input"
                        placeholder="Buscar por título ou arquivo..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <button class="nd-btn nd-btn-primary w-100" type="submit">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
            <?php if ($search || $catFilter): ?>
                <div class="col-sm-6 col-md-2">
                     <a href="/admin/general-documents" class="nd-btn nd-btn-outline w-100">
                        <i class="bi bi-x-lg me-1"></i> Limpar
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$documents): ?>
            <div class="text-center py-5">
                 <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhum documento encontrado</p>
                <p class="text-muted small mb-3">
                    <?= ($search || $catFilter) ? 'Tente ajustar os filtros da sua busca.' : 'O acervo digital está vazio no momento.' ?>
                </p>
                <?php if (!$search && !$catFilter): ?>
                    <button class="btn btn-sm btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#createDocumentModal">
                        Realizar primeira publicação
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Categoria</th>
                            <th>Arquivo</th>
                            <th>Status & Vigência</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <?php
                                // Determinar ícone por extensão
                                $ext = strtolower(pathinfo($doc['file_original_name'] ?? '', PATHINFO_EXTENSION));
                                $iconClass = match($ext) {
                                    'pdf' => 'bi-file-earmark-pdf-fill text-danger',
                                    'doc', 'docx' => 'bi-file-earmark-word-fill text-primary',
                                    'xls', 'xlsx', 'csv' => 'bi-file-earmark-excel-fill text-success',
                                    'ppt', 'pptx'  => 'bi-file-earmark-ppt-fill text-warning',
                                    'zip', 'rar'   => 'bi-file-earmark-zip-fill text-secondary',
                                    'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill text-info',
                                    default => 'bi-file-earmark-text-fill text-secondary'
                                };
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="nd-avatar nd-avatar-sm bg-light border" style="font-size: 1.2rem;">
                                            <i class="bi <?= $iconClass ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if (!empty($doc['description'])): ?>
                                                <div class="small text-muted text-truncate" style="max-width: 250px;">
                                                    <?= htmlspecialchars($doc['description'], ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-folder2 text-muted"></i>
                                        <?= htmlspecialchars($doc['category_name'] ?? 'Geral', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-dark font-monospace text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($doc['file_original_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($doc['file_original_name'] ?? 'arquivo', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="text-muted">
                                            <?php
                                            $bytes = (int)($doc['file_size'] ?? 0);
                                            if ($bytes < 1024) echo $bytes . ' B';
                                            elseif ($bytes < 1024 * 1024) echo number_format($bytes / 1024, 1, ',', '.') . ' KB';
                                            else echo number_format($bytes / (1024 * 1024), 2, ',', '.') . ' MB';
                                            ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div>
                                            <?php if ((int)$doc['is_active'] === 1): ?>
                                                <span class="nd-badge nd-badge-success">Publicado</span>
                                            <?php else: ?>
                                                <span class="nd-badge nd-badge-secondary">Arquivado</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted d-flex align-items-center gap-1">
                                            <i class="bi bi-calendar-event"></i>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($doc['published_at'])) ?? 'Agora', ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                         <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#toggleModal"
                                            data-doc-id="<?= (int)$doc['id'] ?>"
                                            data-doc-active="<?= (int)$doc['is_active'] ?>"
                                            title="<?= (int)$doc['is_active'] === 1 ? 'Arquivar Documento' : 'Publicar Documento' ?>">
                                            <i class="bi <?= (int)$doc['is_active'] === 1 ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                        </button>

                                        <a href="/admin/general-documents/<?= (int)$doc['id'] ?>/edit"
                                            class="nd-btn nd-btn-outline nd-btn-sm"
                                            title="Editar Informações">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button type="button" class="nd-btn nd-btn-outline nd-btn-sm text-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-doc-id="<?= (int)$doc['id'] ?>"
                                            data-doc-title="<?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="Excluir Permanentemente">
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
                Exibindo <strong><?= count($documents) ?></strong> documentos no acervo
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Criar Novo Documento -->
<div class="modal fade" id="createDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-cloud-arrow-up text-primary fs-5"></i>
                    <h5 class="modal-title fw-bold text-dark">Nova Publicação</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" action="/admin/general-documents" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="nd-label">Título da Publicação <span class="text-danger">*</span></label>
                            <div class="nd-input-group">
                                <input type="text" class="nd-input w-100" name="title" required placeholder="Ex: Manual de Conduta, Regulamento Interno..." value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="padding-left: 2.5rem;">
                                <i class="bi bi-type-h1 nd-input-icon"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="nd-label">Categoria <span class="text-danger">*</span></label>
                            <div class="nd-input-group">
                                <select class="nd-input form-select w-100" name="category_id" required style="padding-left: 2.5rem;">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= (int)$cat['id'] ?>" <?= ((int)($old['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="bi bi-folder nd-input-icon"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="nd-label">Arquivo Digital <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
                            <div class="form-text small">Arquivos PDF, Office, Imagens e ZIP (Max: 50MB).</div>
                        </div>

                        <div class="col-md-12">
                            <label class="nd-label">Descrição</label>
                            <textarea class="nd-input w-100" name="description" rows="3" placeholder="Descreva brevemente o conteúdo e objetivo deste documento..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="nd-label">Vigência Inicial</label>
                             <div class="nd-input-group">
                                <input type="date" class="nd-input w-100" name="published_at" value="<?= htmlspecialchars($old['published_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="padding-left: 2.5rem;">
                                <i class="bi bi-calendar-event nd-input-icon"></i>
                            </div>
                            <div class="form-text small">Deixe vazio para publicação imediata.</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch p-0 m-0 d-flex gap-3 align-items-center mt-3 ps-3">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="createIsActive" name="is_active" value="1" <?= (!isset($old['is_active']) || (int)$old['is_active'] === 1 ? 'checked' : '') ?> style="width: 3rem; height: 1.5rem;">
                                <div>
                                    <label class="form-check-label text-dark fw-medium" for="createIsActive">
                                        Publicar no Portal
                                    </label>
                                    <div class="small text-muted">Se desmarcado, ficará apenas arquivado.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="nd-btn nd-btn-outline" data-bs-dismiss="modal">
                             <i class="bi bi-x-lg me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="nd-btn nd-btn-primary">
                             <i class="bi bi-check-lg me-1"></i> Publicar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Toggle Status -->
<div class="modal fade" id="toggleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat fs-5 text-primary"></i>
                    <h5 class="modal-title fw-bold">Alterar Visibilidade</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
             <form method="post" id="toggleForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p id="toggleMessage" class="text-dark mb-0 fw-medium"></p>
                    <p class="small text-muted mt-2 mb-0">Esta ação reflete imediatamente no portal do usuário.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-primary nd-btn-sm">
                        <i class="bi bi-check-circle me-1"></i> Confirmar Alteração
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 bg-danger-subtle text-danger">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trash-fill fs-5"></i>
                    <h5 class="modal-title fw-bold">Remover Publicação</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="deleteForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-dark mb-0">
                        Tem certeza que deseja excluir o documento <strong id="deleteTitle"></strong>?
                    </p>
                    <p class="text-muted small mt-2 mb-0">Esta ação é irreversível e removerá o arquivo do servidor.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-sm bg-danger text-white border-danger hover-danger-fill">
                        <i class="bi bi-trash me-1"></i> Excluir Definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const toggleModal = document.getElementById('toggleModal');
const toggleForm = document.getElementById('toggleForm');
const toggleMessage = document.getElementById('toggleMessage');

toggleModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const docId = btn.dataset.docId;
  const isActive = parseInt(btn.dataset.docActive);
  
  toggleMessage.textContent = isActive === 1 
    ? 'Deseja arquivar este documento? Ele deixará de ser visível no portal.'
    : 'Deseja publicar este documento? Ele ficará disponível para download.';
  
  toggleForm.action = `/admin/general-documents/${docId}/toggle`;
});

const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteTitle = document.getElementById('deleteTitle');

deleteModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const docId = btn.dataset.docId;
  const title = btn.dataset.docTitle;
  
  deleteTitle.textContent = title;
  deleteForm.action = `/admin/general-documents/${docId}/delete`;
});
</script>

<style>
    @media (max-width: 575.98px) {
        .nd-page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .nd-page-header > .d-flex {
            width: 100%;
        }
        .nd-page-header .nd-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
