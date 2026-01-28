<?php
/**
 * Espera em $viewData:
 * - array $category
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - array $old (dados antigos do formulário)
 */
$category = $viewData['category'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];

// Se não há dados antigos, usa os dados da categoria
if (empty($old)) {
  $old = $category;
}

$catId = (int)($category['id'] ?? 0);
if (!$catId) {
  http_response_code(404);
  echo 'Categoria não encontrada.';
  exit;
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/document-categories" class="text-decoration-none">
            <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
                <i class="bi bi-arrow-left text-white"></i>
            </div>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Gerenciar Categoria</h1>
            <p class="text-muted mb-0 small">Edição de classificação documental</p>
        </div>
    </div>
    <a href="/admin/document-categories" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-list-ul me-1"></i>
        Voltar à Listagem
    </a>
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

<div class="row">
    <div class="col-lg-8">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados da Categoria</h5>
            </div>
            <div class="nd-card-body">
                <form method="post" action="/admin/document-categories/<?= $catId ?>" novalidate id="editForm">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="_method" value="PUT">

                    <!-- Nome -->
                    <div class="mb-4">
                        <label for="name" class="nd-label">Identificação da Classificação <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text" class="nd-input <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                                id="name" name="name" 
                                value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ex: Demonstrativos Financeiros..." 
                                required style="padding-left: 2.5rem;">
                            <i class="bi bi-tag nd-input-icon"></i>
                        </div>
                        <?php if (!empty($errors['name'])): ?>
                            <div class="text-danger small mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <label for="description" class="nd-label">Descrição</label>
                        <textarea class="nd-input w-100 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
                            id="description" name="description" rows="3" style="resize: none;"
                            placeholder="Descreva o propósito desta classificação..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="text-danger small mt-1"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Ordem de Exibição -->
                    <div class="mb-4">
                        <label for="sort_order" class="nd-label">Prioridade de Listagem</label>
                        <div class="nd-input-group">
                             <input type="number" class="nd-input <?= !empty($errors['sort_order']) ? 'is-invalid' : '' ?>" 
                                id="sort_order" name="sort_order" 
                                value="<?= htmlspecialchars($old['sort_order'] ?? '1', ENT_QUOTES, 'UTF-8') ?>" 
                                min="1" max="999" style="padding-left: 2.5rem;">
                            <i class="bi bi-sort-numeric-down nd-input-icon"></i>
                        </div>
                        <div class="form-text small">Sequência numérica para ordenação visual na listagem.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="/admin/document-categories" class="nd-btn nd-btn-outline">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Metadata -->
        <div class="nd-card bg-light border-0 mb-4">
            <div class="nd-card-body py-3">
                <h6 class="nd-card-title small text-muted mb-3 border-bottom pb-2">Metadados do Registro</h6>
                <div class="d-flex flex-column gap-3 small">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">ID Sistema</span>
                        <span class="font-monospace bg-white border px-2 py-1 rounded">#<?= $catId ?></span>
                    </div>
                     <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Criado em</span>
                        <span class="text-dark fw-medium">
                            <i class="bi bi-calendar3 me-1"></i> 
                            <?= htmlspecialchars(date('d/m/Y', strtotime($category['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="nd-card border-danger">
            <div class="nd-card-header bg-danger text-white d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <h5 class="nd-card-title mb-0 text-white">Zona de Perigo</h5>
            </div>
            <div class="nd-card-body">
                <p class="small text-muted mb-3">
                    A remoção desta classificação é <strong>definitiva</strong>. Certifique-se de que não existem documentos dependentes desta categoria.
                </p>
                <button type="button" class="nd-btn nd-btn-sm w-100 bg-danger text-white border-danger hover-danger-fill" 
                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-2"></i> Remover Categoria
                </button>
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
                     <i class="bi bi-trash-fill fs-5"></i>
                    <h5 class="modal-title fw-bold">Remover Classificação</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="/admin/document-categories/<?= $catId ?>/delete">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-dark mb-0">
                        Tem certeza que deseja excluir a categoria <strong class="text-dark"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></strong>?
                    </p>
                    <p class="text-muted small mt-2 mb-0">
                        Esta ação não pode ser desfeita e removerá permanentemente este registro.
                    </p>
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
