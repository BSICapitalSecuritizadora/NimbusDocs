<?php
/**
 * Espera em $viewData:
 * - array $document
 * - array $categories
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - array $old (dados antigos do formulário)
 */
$document = $viewData['document'] ?? [];
$categories = $viewData['categories'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];

// Se não há dados antigos, usa os dados do documento
if (empty($old)) {
  $old = $document;
}

$docId = (int)($document['id'] ?? 0);
if (!$docId) {
  http_response_code(404);
  echo 'Documento não encontrado.';
  exit;
}
?>

<!-- Page Header -->
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Editar Documento</h1>
            <p class="text-muted mb-0 small">#<?= $docId ?> &bull; Atualize as informações do arquivo</p>
        </div>
    </div>
    <a href="/admin/general-documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
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
    <div class="nd-card h-100">
      <div class="nd-card-header d-flex align-items-center gap-2">
         <div class="nd-avatar nd-avatar-sm" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
            <i class="bi bi-pencil-square"></i>
         </div>
         <h5 class="nd-card-title mb-0">Detalhes do Documento</h5>
      </div>
      <div class="nd-card-body">
        <form method="post" action="/admin/general-documents/<?= $docId ?>" novalidate>
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="_method" value="PUT">

          <!-- Título -->
          <div class="mb-4">
            <label for="title" class="nd-label">Título <span class="text-danger">*</span></label>
            <div class="nd-input-group">
                <input type="text" class="nd-input <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
                  id="title" name="title" 
                  value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  placeholder="Digite o título do documento" 
                  required style="padding-left: 2.5rem;">
                <i class="bi bi-type-h1 nd-input-icon"></i>
            </div>
            <?php if (!empty($errors['title'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Categoria -->
          <div class="mb-4">
            <label for="category_id" class="nd-label">Categoria <span class="text-danger">*</span></label>
            <div class="nd-input-group">
                <select class="nd-input form-select <?= !empty($errors['category_id']) ? 'is-invalid' : '' ?>" 
                  id="category_id" name="category_id" required style="padding-left: 2.5rem;">
                  <option value="">Selecione uma categoria</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= ((int)($old['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '') ?>>
                      <?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <i class="bi bi-folder nd-input-icon"></i>
            </div>
            <?php if (!empty($errors['category_id'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Descrição -->
          <div class="mb-4">
            <label for="description" class="nd-label">Descrição</label>
            <textarea class="nd-input w-100 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="description" name="description" rows="5" style="resize: none;"
              placeholder="Descreva o conteúdo deste documento..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <div class="d-flex justify-content-end mt-1">
                <small class="text-muted font-size-sm">
                  <span id="charCount">0</span>/1000 caracteres
                </small>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="/admin/general-documents" class="nd-btn nd-btn-outline">Cancelar</a>
            <button type="submit" class="nd-btn nd-btn-primary">
                <i class="bi bi-check-lg me-1"></i> Salvar Alterações
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Painel Lateral -->
  <div class="col-lg-4">
    <!-- Arquivo Atual -->
    <div class="nd-card mb-4">
      <div class="nd-card-header">
        <h6 class="nd-card-title mb-0">Arquivo Atual</h6>
      </div>
      <div class="nd-card-body">
        <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded border border-light">
             <div class="nd-avatar border" style="background: #fff; color: var(--nd-primary-700);">
                <i class="bi bi-file-earmark-text"></i>
             </div>
             <div class="overflow-hidden">
                 <div class="fw-bold text-dark text-truncate" title="<?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>">
                     <?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                 </div>
                 <div class="small text-muted">
                    <?php
                    $bytes = (int)($document['file_size'] ?? 0);
                    if ($bytes < 1024) echo $bytes . ' B';
                    elseif ($bytes < 1024 * 1024) echo round($bytes / 1024, 2) . ' KB';
                    else echo round($bytes / (1024 * 1024), 2) . ' MB';
                    ?>
                    &bull; <?= htmlspecialchars($document['file_mime'] ?? 'UNK', ENT_QUOTES, 'UTF-8') ?>
                 </div>
             </div>
        </div>
        
        <?php if (!empty($document['file_path'])): ?>
          <a href="<?= htmlspecialchars($document['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
            class="nd-btn nd-btn-outline w-100" 
            target="_blank" download>
            <i class="bi bi-download me-1"></i> Baixar Arquivo
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Status e Ações -->
    <div class="nd-card mb-4">
      <div class="nd-card-header">
        <h6 class="nd-card-title mb-0">Status e Ações</h6>
      </div>
      <div class="nd-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
           <label class="nd-label mb-0">Visibilidade</label>
            <?php if ((int)$document['is_active'] === 1): ?>
              <span class="nd-badge nd-badge-success">Ativo</span>
            <?php else: ?>
              <span class="nd-badge nd-badge-secondary">Inativo</span>
            <?php endif; ?>
        </div>
        
        <form method="post" action="/admin/general-documents/<?= $docId ?>/toggle" class="mb-3">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="nd-btn w-100 <?= (int)$document['is_active'] === 1 ? 'nd-btn-outline text-muted' : 'nd-btn-primary' ?>">
            <i class="bi <?= (int)$document['is_active'] === 1 ? 'bi-eye-slash' : 'bi-eye' ?> me-1"></i>
            <?= (int)$document['is_active'] === 1 ? 'Ocultar Documento' : 'Publicar Documento' ?>
          </button>
        </form>

        <div class="border-top pt-3">
            <form method="post" action="/admin/general-documents/<?= $docId ?>/delete" onsubmit="return confirm('Tem certeza que deseja deletar este documento? Ação irreversível.');">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="nd-btn w-100 bg-danger text-white border-0 hover-opacity-90">
                <i class="bi bi-trash me-1"></i> Excluir Permanentemente
            </button>
            </form>
        </div>
      </div>
    </div>

    <!-- Metadados -->
    <div class="nd-card bg-light border-0">
      <div class="nd-card-body">
        <h6 class="nd-card-title mb-3 text-muted small text-uppercase">Metadados do Sistema</h6>
        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
            <li class="d-flex justify-content-between text-muted">
                <span>ID Interno:</span>
                <span class="font-monospace text-dark">#<?= (int)$document['id'] ?></span>
            </li>
            <li class="d-flex justify-content-between text-muted">
                <span>Criado em:</span>
                <span class="text-dark"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($document['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <li class="d-flex justify-content-between text-muted">
                <span>Publicado em:</span>
                <span class="text-dark"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($document['published_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <li class="d-flex justify-content-between text-muted">
                <span>Admin ID:</span>
                <span class="text-dark">#<?= (int)$document['created_by_admin'] ?></span>
            </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
// Contador de caracteres
const descriptionField = document.getElementById('description');
const charCount = document.getElementById('charCount');

if (descriptionField && charCount) {
    const updateCount = () => {
        charCount.textContent = descriptionField.value.length;
    };
    descriptionField.addEventListener('input', updateCount);
    updateCount(); // Inicializa
}
</script>
