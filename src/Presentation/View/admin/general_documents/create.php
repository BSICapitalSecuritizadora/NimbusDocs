<?php
/**
 * Espera em $viewData:
 * - array $categories
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - array $old (dados antigos do formulário)
 */
$categories = $viewData['categories'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];
?>

<div class="mb-4">
  <a href="/admin/general-documents" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-chevron-left"></i> Voltar
  </a>
  <h1 class="h4">Novo Documento Geral</h1>
</div>

<?php if (!empty($flash['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="post" action="/admin/general-documents" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

      <div class="row">
        <div class="col-md-8">
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
            <small class="form-text text-muted d-block mt-1">Entre 3 e 255 caracteres</small>
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
              id="description" name="description" rows="4" 
              placeholder="Digite uma descrição detalhada do documento"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="form-text text-muted d-block mt-1">
              <span id="charCount">0</span>/1000 caracteres
            </small>
          </div>

          <!-- Data de Publicação -->
          <div class="mb-3">
            <label for="published_at" class="form-label">Data de publicação</label>
            <input type="date" class="form-control" id="published_at" name="published_at"
              value="<?= htmlspecialchars($old['published_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <small class="form-text text-muted d-block mt-1">Deixe em branco para publicar imediatamente</small>
          </div>

          <!-- Arquivo -->
          <div class="mb-3">
            <label for="file" class="form-label">Arquivo <span class="text-danger">*</span></label>
            <input type="file" class="form-control <?= !empty($errors['file']) ? 'is-invalid' : '' ?>" 
              id="file" name="file" required 
              accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png,.gif,.webp">
            <?php if (!empty($errors['file'])): ?>
              <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['file'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <small class="form-text text-muted d-block mt-1">
              <strong>Máximo 50 MB</strong><br>
              Formatos aceitos: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, JPG, JPEG, PNG, GIF, WEBP
            </small>
            <div id="fileInfo" class="mt-2" style="display: none;">
              <small class="d-block">Arquivo: <strong id="fileName"></strong></small>
              <small class="text-muted d-block">Tamanho: <strong id="fileSize"></strong></small>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <!-- Painel lateral -->
          <div class="card bg-light border-0">
            <div class="card-body">
              <h6 class="card-title">Configurações</h6>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                  value="1" <?= (!isset($old['is_active']) || (int)$old['is_active'] === 1 ? 'checked' : '') ?>>
                <label class="form-check-label" for="is_active">
                  Ativar documento
                </label>
                <small class="d-block text-muted">Documento será visível no portal se ativado</small>
              </div>

              <hr>

              <h6 class="card-title mt-3">Informações</h6>
              <small class="text-muted d-block">
                <i class="bi bi-info-circle"></i> 
                Documento será salvo com as informações fornecidas acima e ficará disponível para usuários do portal.
              </small>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <a href="/admin/general-documents" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Criar documento</button>
      </div>
    </form>
  </div>
</div>

<script>
// Contador de caracteres
const descriptionField = document.getElementById('description');
const charCount = document.getElementById('charCount');

descriptionField?.addEventListener('input', function() {
  charCount.textContent = this.value.length;
});

// Exibir informações do arquivo selecionado
const fileInput = document.getElementById('file');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

fileInput?.addEventListener('change', function() {
  if (this.files && this.files.length > 0) {
    const file = this.files[0];
    fileName.textContent = file.name;
    
    let sizeText = '';
    const sizeMB = file.size / (1024 * 1024);
    if (sizeMB > 50) {
      sizeText = sizeMB.toFixed(2) + ' MB <span class="text-danger">(Excede limite!)</span>';
    } else {
      sizeText = sizeMB.toFixed(2) + ' MB';
    }
    fileSize.innerHTML = sizeText;
    fileInfo.style.display = 'block';
  } else {
    fileInfo.style.display = 'none';
  }
});

// Carregar caracteres existentes
if (descriptionField) {
  charCount.textContent = descriptionField.value.length;
}
</script>
