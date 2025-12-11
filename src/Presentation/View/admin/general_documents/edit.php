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

<div class="mb-4">
  <a href="/admin/general-documents" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-chevron-left"></i> Voltar
  </a>
  <h1 class="h4">Editar Documento</h1>
  <p class="text-muted small mb-0">#<?= (int)$document['id'] ?> | Criado em <?= htmlspecialchars($document['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
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
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <form method="post" action="/admin/general-documents/<?= $docId ?>" novalidate>
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="_method" value="PUT">

          <!-- Título -->
          <div class="mb-3">
            <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
              id="title" name="title" 
              value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
              placeholder="Digite o título do documento" 
              required>
            <?php if (!empty($errors['title'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Categoria -->
          <div class="mb-3">
            <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
            <select class="form-select <?= !empty($errors['category_id']) ? 'is-invalid' : '' ?>" 
              id="category_id" name="category_id" required>
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

          <!-- Descrição -->
          <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="description" name="description" rows="4"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="form-text text-muted d-block mt-1">
              <span id="charCount">0</span>/1000 caracteres
            </small>
          </div>

          <div class="mt-4">
            <a href="/admin/general-documents" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Painel Lateral -->
  <div class="col-lg-4">
    <!-- Arquivo Atual -->
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Arquivo Atual</h6>
      </div>
      <div class="card-body">
        <div class="mb-2">
          <small class="text-muted d-block">Nome:</small>
          <small class="text-monospace"><strong><?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></strong></small>
        </div>
        <div class="mb-2">
          <small class="text-muted d-block">Tamanho:</small>
          <small>
            <?php
            $bytes = (int)($document['file_size'] ?? 0);
            if ($bytes < 1024) {
              echo $bytes . ' B';
            } elseif ($bytes < 1024 * 1024) {
              echo round($bytes / 1024, 2) . ' KB';
            } else {
              echo round($bytes / (1024 * 1024), 2) . ' MB';
            }
            ?>
          </small>
        </div>
        <div class="mb-3">
          <small class="text-muted d-block">Tipo:</small>
          <small><?= htmlspecialchars($document['file_mime'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></small>
        </div>
        <?php if (!empty($document['file_path'])): ?>
          <a href="<?= htmlspecialchars($document['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
            class="btn btn-sm btn-outline-primary w-100" 
            target="_blank" download>
            <i class="bi bi-download"></i> Download
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Status -->
    <div class="card mb-3">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Status</h6>
      </div>
      <div class="card-body">
        <div class="mb-2">
          <small class="text-muted d-block">Ativado:</small>
          <div>
            <?php if ((int)$document['is_active'] === 1): ?>
              <span class="badge bg-success">Ativo</span>
            <?php else: ?>
              <span class="badge bg-secondary">Inativo</span>
            <?php endif; ?>
          </div>
        </div>

        <form method="post" action="/admin/general-documents/<?= $docId ?>/toggle" class="mt-3">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-sm btn-outline-warning w-100">
            <?= (int)$document['is_active'] === 1 ? 'Desativar' : 'Ativar' ?>
          </button>
        </form>

        <form method="post" action="/admin/general-documents/<?= $docId ?>/delete" class="mt-2" onsubmit="return confirm('Tem certeza que deseja deletar este documento?');">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger w-100">
            <i class="bi bi-trash"></i> Deletar
          </button>
        </form>
      </div>
    </div>

    <!-- Metadados -->
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Metadados</h6>
      </div>
      <div class="card-body">
        <div class="mb-2">
          <small class="text-muted">ID:</small><br>
          <small><code>#<?= (int)$document['id'] ?></code></small>
        </div>
        <div class="mb-2">
          <small class="text-muted">Criado em:</small><br>
          <small><?= htmlspecialchars($document['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
        </div>
        <div class="mb-2">
          <small class="text-muted">Publicado em:</small><br>
          <small><?= htmlspecialchars($document['published_at'] ?? 'Agora', ENT_QUOTES, 'UTF-8') ?></small>
        </div>
        <div>
          <small class="text-muted">Criado por:</small><br>
          <small>Admin #<?= (int)$document['created_by_admin'] ?></small>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Contador de caracteres
const descriptionField = document.getElementById('description');
const charCount = document.getElementById('charCount');

descriptionField?.addEventListener('input', function() {
  charCount.textContent = this.value.length;
});

// Carregar caracteres existentes
if (descriptionField) {
  charCount.textContent = descriptionField.value.length;
}
</script>
