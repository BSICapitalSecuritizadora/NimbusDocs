<?php
/**
 * Espera em $viewData:
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - array $old (dados antigos do formulário)
 */
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];
?>

<div class="mb-4">
  <a href="/admin/document-categories" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-chevron-left"></i> Voltar
  </a>
  <h1 class="h4">Nova Categoria de Documentos</h1>
</div>

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
        <form method="post" action="/admin/document-categories" novalidate>
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

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
            <small class="form-text text-muted d-block mt-1">Deve ser único e descritivo</small>
          </div>

          <!-- Descrição -->
          <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="description" name="description" rows="3" 
              placeholder="Descreva brevemente o propósito desta categoria"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="form-text text-muted d-block mt-1">Máximo 500 caracteres</small>
          </div>

          <!-- Ordem de Exibição -->
          <div class="mb-4">
            <label for="sort_order" class="form-label">Ordem de Exibição</label>
            <input type="number" class="form-control" 
              id="sort_order" name="sort_order" 
              value="<?= htmlspecialchars($old['sort_order'] ?? '1', ENT_QUOTES, 'UTF-8') ?>" 
              min="1" max="999">
            <small class="form-text text-muted d-block mt-1">
              Categorias com menor número aparecem primeiro no portal (ex: 1 aparece antes de 2)
            </small>
          </div>

          <div>
            <a href="/admin/document-categories" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Criar categoria</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card bg-light border-0">
      <div class="card-body">
        <h6 class="card-title mb-3">
          <i class="bi bi-info-circle"></i> Dicas
        </h6>
        <ul class="small text-muted mb-0">
          <li class="mb-2">
            <strong>Nome:</strong> Use nomes claros e concisos (ex: "Políticas e Procedimentos")
          </li>
          <li class="mb-2">
            <strong>Descrição:</strong> Ajuda os usuários a entenderem o conteúdo da categoria
          </li>
          <li class="mb-2">
            <strong>Ordem:</strong> Use números sequenciais para organizar visualmente (10, 20, 30...)
          </li>
          <li class="mb-2">
            <strong>Organização:</strong> Cada categoria pode conter múltiplos documentos
          </li>
          <li>
            <strong>Exclusão:</strong> Você pode deletar uma categoria vazia a qualquer momento
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
