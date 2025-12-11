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
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h4 mb-1">Documentos Gerais</h1>
    <p class="text-muted small mb-0">
      Gerencie documentos da plataforma que serão disponibilizados aos usuários do portal.
    </p>
  </div>
  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createDocumentModal">
    <i class="bi bi-plus-lg"></i> Novo documento
  </button>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Listagem de Documentos -->
<div class="card">
  <div class="card-body">
    <?php if (!$documents): ?>
      <p class="text-muted small mb-0">Nenhum documento cadastrado. <a href="#createDocumentModal" data-bs-toggle="modal">Criar um novo</a></p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>Categoria</th>
              <th>Arquivo</th>
              <th>Tamanho</th>
              <th>Publicado</th>
              <th>Status</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($documents as $doc): ?>
              <tr>
                <td><small>#<?= (int)$doc['id'] ?></small></td>
                <td>
                  <strong><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                  <?php if (!empty($doc['description'])): ?>
                    <br><small class="text-muted"><?= htmlspecialchars(substr($doc['description'], 0, 50) . (strlen($doc['description']) > 50 ? '...' : ''), ENT_QUOTES, 'UTF-8') ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <small><?= htmlspecialchars($doc['category_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></small>
                </td>
                <td>
                  <small class="text-monospace"><?= htmlspecialchars($doc['file_original_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                </td>
                <td>
                  <small class="text-muted">
                    <?php 
                    $bytes = (int)($doc['file_size'] ?? 0);
                    if ($bytes < 1024) {
                      echo $bytes . ' B';
                    } elseif ($bytes < 1024 * 1024) {
                      echo round($bytes / 1024, 2) . ' KB';
                    } else {
                      echo round($bytes / (1024 * 1024), 2) . ' MB';
                    }
                    ?>
                  </small>
                </td>
                <td>
                  <small class="text-muted">
                    <?= htmlspecialchars($doc['published_at'] ?? 'Agora', ENT_QUOTES, 'UTF-8') ?>
                  </small>
                </td>
                <td>
                  <?php if ((int)$doc['is_active'] === 1): ?>
                    <span class="badge bg-success">Ativo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Inativo</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a href="/admin/general-documents/<?= (int)$doc['id'] ?>/edit"
                    class="btn btn-sm btn-outline-secondary"
                    title="Editar">
                    <i class="bi bi-pencil"></i>
                  </a>

                  <button type="button" class="btn btn-sm btn-outline-warning" 
                    data-bs-toggle="modal" 
                    data-bs-target="#toggleModal"
                    data-doc-id="<?= (int)$doc['id'] ?>"
                    data-doc-active="<?= (int)$doc['is_active'] ?>"
                    title="<?= (int)$doc['is_active'] === 1 ? 'Desativar' : 'Ativar' ?>">
                    <i class="bi <?= (int)$doc['is_active'] === 1 ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                  </button>

                  <button type="button" class="btn btn-sm btn-outline-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteModal"
                    data-doc-id="<?= (int)$doc['id'] ?>"
                    data-doc-title="<?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>"
                    title="Deletar">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal: Criar Novo Documento -->
<div class="modal fade" id="createDocumentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Novo Documento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="/admin/general-documents" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3">
            <label for="createTitle" class="form-label">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
              id="createTitle" name="title" value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
              placeholder="Digite o título do documento" required>
            <?php if (!empty($errors['title'])): ?>
              <div class="invalid-feedback"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="createCategory" class="form-label">Categoria <span class="text-danger">*</span></label>
            <select class="form-select <?= !empty($errors['category_id']) ? 'is-invalid' : '' ?>" 
              id="createCategory" name="category_id" required>
              <option value="">Selecione uma categoria</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= ((int)($old['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '') ?>>
                  <?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['category_id'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="createDescription" class="form-label">Descrição</label>
            <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="createDescription" name="description" rows="3" 
              placeholder="Digite uma descrição do documento"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="form-text text-muted d-block mt-1">Máximo 1000 caracteres</small>
          </div>

          <div class="mb-3">
            <label for="createFile" class="form-label">Arquivo <span class="text-danger">*</span></label>
            <input type="file" class="form-control <?= !empty($errors['file']) ? 'is-invalid' : '' ?>" 
              id="createFile" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
            <?php if (!empty($errors['file'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['file'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="form-text text-muted d-block mt-1">Máximo 50 MB. Formatos aceitos: PDF, DOC, XLS, PPT, ZIP, JPG, PNG</small>
          </div>

          <div class="mb-3">
            <label for="createPublishedAt" class="form-label">Data de publicação</label>
            <input type="date" class="form-control" id="createPublishedAt" name="published_at"
              value="<?= htmlspecialchars($old['published_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <small class="form-text text-muted d-block mt-1">Deixe em branco para publicar agora</small>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="createIsActive" name="is_active" 
              value="1" <?= (!isset($old['is_active']) || (int)$old['is_active'] === 1 ? 'checked' : '') ?>>
            <label class="form-check-label" for="createIsActive">
              Ativar documento (visível no portal)
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Criar documento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Ativar/Desativar -->
<div class="modal fade" id="toggleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Mudar status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="toggleForm">
        <div class="modal-body">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <p id="toggleMessage"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Deletar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="deleteForm">
        <div class="modal-body">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <p class="text-danger">
            <strong>Tem certeza?</strong> Esta ação é irreversível e removerá o documento "<span id="deleteTitle"></span>" permanentemente.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Deletar permanentemente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Modal: Ativar/Desativar
const toggleModal = document.getElementById('toggleModal');
const toggleForm = document.getElementById('toggleForm');
const toggleMessage = document.getElementById('toggleMessage');

toggleModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const docId = btn.dataset.docId;
  const isActive = parseInt(btn.dataset.docActive);
  
  toggleMessage.textContent = isActive === 1 
    ? 'Desativar este documento? Ele não será mais visível no portal.'
    : 'Ativar este documento? Ele ficará visível no portal.';
  
  toggleForm.action = `/admin/general-documents/${docId}/toggle`;
});

// Modal: Deletar
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
