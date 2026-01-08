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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/document-categories" class="text-decoration-none text-muted">
            <div class="nd-avatar nd-avatar-lg bg-light border">
                <i class="bi bi-arrow-left text-muted"></i>
            </div>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Cadastro de Categoria</h1>
            <p class="text-muted mb-0 small">Definição de nova classe documental</p>
        </div>
    </div>
</div>

<?php if (!empty($flash['error'])): ?>
  <div class="nd-alert nd-alert-danger mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i> 
    <div class="nd-alert-text"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-8">
    <div class="nd-card h-100">
      <div class="nd-card-header d-flex align-items-center gap-2">
         <div class="nd-avatar nd-avatar-sm bg-primary bg-opacity-10">
            <i class="bi bi-folder-plus text-primary"></i>
         </div>
         <h5 class="nd-card-title mb-0">Informações da Classificação</h5>
      </div>
      <div class="nd-card-body">
        <form method="post" action="/admin/document-categories" novalidate>
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <!-- Nome -->
          <div class="mb-4">
            <label for="name" class="nd-label">Identificação da Classificação <span class="text-danger">*</span></label>
            <div class="nd-input-group">
                <input type="text" class="nd-input <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                  id="name" name="name" 
                  value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  placeholder="Ex: Demonstrativos Financeiros, Contratos..." 
                  required style="padding-left: 2.5rem;">
                <i class="bi bi-tag nd-input-icon"></i>
            </div>
            <?php if (!empty($errors['name'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="text-muted mt-1 d-block font-size-sm">A identificação deve ser única para manutenção da integridade.</small>
          </div>

          <!-- Descrição -->
          <div class="mb-4">
            <label for="description" class="nd-label">Descrição</label>
            <textarea class="nd-input w-100 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="description" name="description" rows="4" style="resize: none;"
              placeholder="Descreva o propósito desta classificação documental..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Ordem de Exibição -->
          <div class="mb-4">
            <label for="sort_order" class="nd-label">Prioridade de Listagem</label>
             <div class="d-flex align-items-center gap-3">
                 <div class="nd-input-group w-auto" style="max-width: 150px;">
                    <input type="number" class="nd-input <?= !empty($errors['sort_order']) ? 'is-invalid' : '' ?>" 
                      id="sort_order" name="sort_order" 
                      value="<?= htmlspecialchars($old['sort_order'] ?? '1', ENT_QUOTES, 'UTF-8') ?>" 
                      min="1" max="999" style="padding-left: 2.5rem;">
                    <i class="bi bi-sort-numeric-down nd-input-icon"></i>
                </div>
                <small class="text-muted">
                  <i class="bi bi-info-circle me-1"></i>
                  Sequência numérica para ordenação visual (menor = topo).
                </small>
             </div>
          </div>

          <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="/admin/document-categories" class="nd-btn nd-btn-outline">Cancelar</a>
            <button type="submit" class="nd-btn nd-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Salvar Classificação
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="nd-card bg-light border-0">
      <div class="nd-card-body">
        <h6 class="nd-card-title mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-lightbulb text-warning"></i> Dicas Úteis
        </h6>
        <ul class="nd-list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
          <li class="d-flex align-items-start gap-2">
            <i class="bi bi-check2 text-success mt-1"></i>
            <span><strong>Seja claro:</strong> Use nomes curtos mas descritivos.</span>
          </li>
          <li class="d-flex align-items-start gap-2">
            <i class="bi bi-check2 text-success mt-1"></i>
            <span><strong>Agrupe bem:</strong> Crie categorias amplas para evitar fragmentação excessiva.</span>
          </li>
          <li class="d-flex align-items-start gap-2">
            <i class="bi bi-check2 text-success mt-1"></i>
            <span><strong>Organize:</strong> Use a ordem numérica para destacar as categorias mais importantes no topo.</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
