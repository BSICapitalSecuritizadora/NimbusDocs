<?php
/**
 * Espera em $viewData:
 * - array $categories
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 */
$categories = $viewData['categories'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h4 mb-1">Categorias de Documentos</h1>
    <p class="text-muted small mb-0">
      Organize os documentos gerais em categorias para melhor navegação no portal.
    </p>
  </div>
  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
    <i class="bi bi-plus-lg"></i> Nova categoria
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

<!-- Listagem de Categorias -->
<div class="card">
  <div class="card-body">
    <?php if (!$categories): ?>
      <p class="text-muted small mb-0">Nenhuma categoria cadastrada. <a href="#createCategoryModal" data-bs-toggle="modal">Criar a primeira</a></p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Descrição</th>
              <th>Ordem</th>
              <th>Criada em</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $cat): ?>
              <tr>
                <td><small>#<?= (int)$cat['id'] ?></small></td>
                <td>
                  <strong><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                </td>
                <td>
                  <small class="text-muted">
                    <?= htmlspecialchars(
                      !empty($cat['description']) 
                        ? (strlen($cat['description']) > 50 ? substr($cat['description'], 0, 50) . '...' : $cat['description'])
                        : '—',
                      ENT_QUOTES, 'UTF-8'
                    ) ?>
                  </small>
                </td>
                <td>
                  <small class="text-muted"><?= (int)$cat['sort_order'] ?></small>
                </td>
                <td>
                  <small class="text-muted"><?= htmlspecialchars($cat['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                </td>
                <td class="text-end">
                  <a href="/admin/document-categories/<?= (int)$cat['id'] ?>/edit"
                    class="btn btn-sm btn-outline-secondary"
                    title="Editar">
                    <i class="bi bi-pencil"></i>
                  </a>

                  <button type="button" class="btn btn-sm btn-outline-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteModal"
                    data-cat-id="<?= (int)$cat['id'] ?>"
                    data-cat-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>"
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

<!-- Modal: Criar Nova Categoria -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nova Categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="/admin/document-categories" novalidate>
        <div class="modal-body">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3">
            <label for="createName" class="form-label">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
              id="createName" name="name" 
              placeholder="Ex: Políticas, Manuais, Relatórios" 
              required>
            <?php if (!empty($errors['name'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="createDescription" class="form-label">Descrição</label>
            <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="createDescription" name="description" rows="2" 
              placeholder="Descrição opcional"></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="createSortOrder" class="form-label">Ordem de exibição</label>
            <input type="number" class="form-control" 
              id="createSortOrder" name="sort_order" 
              value="1" min="1" max="999">
            <small class="form-text text-muted d-block mt-1">Categorias com menor número aparecem primeiro</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Criar categoria</button>
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
            <strong>Tem certeza?</strong> Esta ação é irreversível e removerá a categoria "<span id="deleteName"></span>" permanentemente.
          </p>
          <p class="text-muted small mt-3">
            <i class="bi bi-info-circle"></i> 
            Certifique-se de que não há documentos associados a esta categoria antes de deletá-la.
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
// Modal: Deletar
const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteName = document.getElementById('deleteName');

deleteModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const catId = btn.dataset.catId;
  const name = btn.dataset.catName;
  
  deleteName.textContent = name;
  deleteForm.action = `/admin/document-categories/${catId}/delete`;
});
</script>
