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

<div class="mb-4">
  <a href="/admin/document-categories" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-chevron-left"></i> Voltar
  </a>
  <h1 class="h4">Editar Categoria</h1>
  <p class="text-muted small mb-0">#<?= (int)$category['id'] ?> | Criada em <?= htmlspecialchars($category['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> <?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <form method="post" action="/admin/document-categories/<?= $catId ?>" novalidate>
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="_method" value="PUT">

          <!-- Nome -->
          <div class="mb-3">
            <label for="name" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
              id="name" name="name" 
              value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
              placeholder="Ex: Políticas, Manuais, Relatórios" 
              required>
            <?php if (!empty($errors['name'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Descrição -->
          <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="description" name="description" rows="3"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Ordem de Exibição -->
          <div class="mb-4">
            <label for="sort_order" class="form-label">Ordem de Exibição</label>
            <input type="number" class="form-control" 
              id="sort_order" name="sort_order" 
              value="<?= htmlspecialchars($old['sort_order'] ?? '1', ENT_QUOTES, 'UTF-8') ?>" 
              min="1" max="999">
            <small class="form-text text-muted d-block mt-1">
              Categorias com menor número aparecem primeiro
            </small>
          </div>

          <div>
            <a href="/admin/document-categories" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Painel Lateral -->
  <div class="col-lg-6">
    <!-- Informações -->
    <div class="card mb-3 bg-light border-0">
      <div class="card-body">
        <h6 class="card-title mb-3">Informações</h6>
        <div class="mb-2">
          <small class="text-muted">ID:</small><br>
          <small><code>#<?= (int)$category['id'] ?></code></small>
        </div>
        <div class="mb-2">
          <small class="text-muted">Criada em:</small><br>
          <small><?= htmlspecialchars($category['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
        </div>
      </div>
    </div>

    <!-- Ações -->
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Ações</h6>
      </div>
      <div class="card-body">
        <form method="post" action="/admin/document-categories/<?= $catId ?>/delete" onsubmit="return confirm('Tem certeza que deseja deletar esta categoria? Esta ação é irreversível.');">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger w-100">
            <i class="bi bi-trash"></i> Deletar categoria
          </button>
        </form>
        <small class="text-muted d-block mt-3">
          <i class="bi bi-info-circle"></i> 
          Você apenas poderá deletar esta categoria se nenhum documento estiver associado a ela.
        </small>
      </div>
    </div>
  </div>
</div>
