<?php
/**
 * Espera em $viewData:
 * - array $categories
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - string $search (opcional se implementado no controller)
 */
$categories = $viewData['categories'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$search = $_GET['search'] ?? '';

// Filtro simples em PHP se não houver no controller (idealmente deveria ser no repo)
if ($search) {
    $categories = array_filter($categories, fn($c) => stripos($c['name'], $search) !== false);
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-folder-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Categorias de Documentos</h1>
            <p class="text-muted mb-0 small">Classificação e organização documental do portal</p>
        </div>
    </div>
    <button class="nd-btn nd-btn-gold nd-btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-lg me-1"></i>
        Nova Categoria
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

<!-- Categories List -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
         <form class="row g-3 align-items-center" method="get" action="/admin/document-categories">
            <div class="col-sm-6 col-md-5">
                <div class="nd-input-group">
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                        class="nd-input"
                        placeholder="Pesquisar por categoria..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
             <div class="col-sm-6 col-md-4">
                <button class="nd-btn nd-btn-primary" type="submit">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
                 <?php if (!empty($search)): ?>
                    <a href="/admin/document-categories" class="nd-btn nd-btn-outline ms-2">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$categories): ?>
             <div class="text-center py-5">
                 <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-folder-x text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhuma categoria encontrada</p>
                <p class="text-muted small mb-3">Utilize o botão de cadastro para criar uma nova classificação.</p>
                <button class="btn btn-sm btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    Cadastrar Nova Categoria
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;" class="text-center">Ordem</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th>Data de Cadastro</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border font-monospace" style="min-width: 30px;">
                                        <?= (int)$cat['sort_order'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-folder2-open text-primary opacity-75"></i>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($cat['description'])): ?>
                                        <span class="text-muted small">
                                            <?= htmlspecialchars(
                                                 strlen($cat['description']) > 60 ? substr($cat['description'], 0, 60) . '...' : $cat['description'],
                                                ENT_QUOTES, 'UTF-8'
                                            ) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 small text-dark">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($cat['created_at'])) ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <!-- Edit Action (could be distinct page or same modal, here assuming distinct based on existing code) -->
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
                                            title="Remover">
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
                Total de <strong><?= count($categories) ?></strong> categorias cadastradas
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Criar Nova Categoria -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-folder-plus text-primary fs-5"></i>
                    <h5 class="modal-title fw-bold text-dark">Nova Categoria</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="post" action="/admin/document-categories" novalidate>
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="nd-label">Nome da Categoria <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text" class="nd-input <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                                name="name" placeholder="Ex: Contratos, Financeiro..." required style="padding-left: 2.5rem;">
                            <i class="bi bi-tag nd-input-icon"></i>
                        </div>
                        <?php if (!empty($errors['name'])): ?>
                            <div class="text-danger small mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                         <small class="text-muted mt-1 d-block font-size-sm">O nome deve ser único no sistema.</small>
                    </div>

                    <div class="mb-3">
                        <label class="nd-label">Descrição (Opcional)</label>
                        <textarea class="nd-input w-100" name="description" rows="3" placeholder="Breve descrição sobre os documentos desta categoria"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="nd-label">Ordem de Exibição</label>
                        <div class="nd-input-group">
                            <input type="number" class="nd-input w-100" name="sort_order" value="1" min="1" style="padding-left: 2.5rem;">
                            <i class="bi bi-sort-numeric-down nd-input-icon"></i>
                        </div>
                        <div class="form-text small">Define a posição da categoria na listagem.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="nd-btn nd-btn-outline" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Salvar Categoria
                        </button>
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
            <div class="modal-header border-bottom py-3 bg-danger-subtle text-danger">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h5 class="modal-title fw-bold">Excluir Categoria</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="deleteForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-dark mb-0">
                        Você está prestes a excluir a categoria <strong id="deleteName"></strong>.
                    </p>
                     <p class="text-muted small mt-2 mb-0">
                        Isso removerá a categoria do sistema. Certifique-se que não há documentos importantes vinculados exclusivamente a ela.
                    </p>
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
