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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-folder-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Categorias de Documentos</h1>
            <p class="text-muted mb-0 small">Gerencie as categorias para organizar os documentos no portal</p>
        </div>
    </div>
    <button class="nd-btn nd-btn-gold nd-btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-lg me-1"></i>
        Nova Categoria
    </button>
</div>

<?php if (!empty($flash['success'])): ?>
    <div class="nd-alert nd-alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
    <div class="nd-alert nd-alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<!-- Categories List -->
<div class="nd-card">
    <div class="nd-card-body p-0">
        <?php if (!$categories): ?>
             <div class="text-center py-5">
                <i class="bi bi-folder-active text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-2">Nenhuma categoria cadastrada.</p>
                <button class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    Criar a primeira categoria
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Ordem</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Criada em</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        <?= (int)$cat['sort_order'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <?= htmlspecialchars(
                                            !empty($cat['description']) 
                                                ? (strlen($cat['description']) > 60 ? substr($cat['description'], 0, 60) . '...' : $cat['description'])
                                                : '—',
                                            ENT_QUOTES, 'UTF-8'
                                        ) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($cat['created_at'])) ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="/admin/document-categories/<?= (int)$cat['id'] ?>/edit"
                                            class="nd-btn nd-btn-outline nd-btn-sm"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button type="button" class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-cat-id="<?= (int)$cat['id'] ?>"
                                            data-cat-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
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
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form method="post" action="/admin/document-categories" novalidate>
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="nd-label">Nome <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text" class="nd-input <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                                name="name" placeholder="Ex: Financeiro" required style="padding-left: 2.5rem;">
                            <i class="bi bi-tag nd-input-icon"></i>
                        </div>
                        <?php if (!empty($errors['name'])): ?>
                            <div class="text-danger small mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="nd-label">Descrição</label>
                        <textarea class="nd-input w-100" name="description" rows="2" placeholder="Opcional"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="nd-label">Ordem de Exibição</label>
                        <input type="number" class="nd-input w-100" name="sort_order" value="1" min="1">
                        <div class="form-text small">Menor número aparece primeiro.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="nd-btn nd-btn-outline" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="nd-btn nd-btn-primary">Criar Categoria</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-danger">Excluir Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="deleteForm">
                <div class="modal-body py-0">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-muted">
                        Tem certeza que deseja excluir a categoria <strong id="deleteName" class="text-dark"></strong>?
                        <br><span class="text-danger small">Isso pode afetar documentos vinculados.</span>
                    </p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-sm bg-danger text-white border-danger">Excluir Permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
