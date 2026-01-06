<?php
/**
 * Espera em $viewData:
 * - array $document (optional initial data)
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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
            <i class="bi bi-plus-lg"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Novo Documento</h1>
            <p class="text-muted mb-0 small">Adicione um novo arquivo para download no portal</p>
        </div>
    </div>
    <a href="/admin/general-documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
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
         <div class="nd-avatar nd-avatar-sm" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
            <i class="bi bi-file-earmark-plus"></i>
         </div>
         <h5 class="nd-card-title mb-0">Dados do Documento</h5>
      </div>
      <div class="nd-card-body">
        <form method="post" action="/admin/general-documents" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <!-- Título -->
          <div class="mb-4">
            <label for="title" class="nd-label">Título <span class="text-danger">*</span></label>
            <div class="nd-input-group">
                <input type="text" class="nd-input <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
                  id="title" name="title" 
                  value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  placeholder="Ex: Manual de Conduta 2026" 
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
                  <option value="">Selecione uma categoria...</option>
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
              id="description" name="description" rows="4" style="resize: none;"
              placeholder="Descreva o conteúdo do documento para facilitar a busca..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (!empty($errors['description'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </div>

          <!-- Arquivo -->
          <div class="mb-4">
            <label for="file" class="nd-label">Arquivo <span class="text-danger">*</span></label>
            <div class="nd-input p-1">
                <input type="file" class="form-control border-0 bg-transparent <?= !empty($errors['file']) ? 'is-invalid' : '' ?>" 
                  id="file" name="file" required 
                  accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png,.gif,.webp">
            </div>
            <?php if (!empty($errors['file'])): ?>
              <div class="text-danger small mt-1"><?= htmlspecialchars($errors['file'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
             <div id="fileInfo" class="mt-2 p-2 bg-light rounded border border-light" style="display: none;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-paperclip text-muted"></i>
                    <div>
                        <span id="fileName" class="d-block fw-bold text-dark small"></span>
                        <span id="fileSize" class="d-block text-muted small"></span>
                    </div>
                </div>
            </div>
          </div>

          <!-- Data de Publicação -->
          <div class="mb-4">
            <label for="published_at" class="nd-label">Data de Publicação</label>
            <div class="nd-input-group">
                <input type="date" class="nd-input" id="published_at" name="published_at"
                  value="<?= htmlspecialchars($old['published_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="padding-left: 2.5rem;">
                <i class="bi bi-calendar3 nd-input-icon"></i>
            </div>
            <small class="text-muted mt-1 d-block font-size-sm">Deixe vazio para publicar imediatamente.</small>
          </div>

          <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="/admin/general-documents" class="nd-btn nd-btn-outline">Cancelar</a>
            <button type="submit" class="nd-btn nd-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Criar Documento
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="nd-card mb-4">
      <div class="nd-card-header">
        <h6 class="nd-card-title mb-0">Visibilidade</h6>
      </div>
      <div class="nd-card-body">
         <div class="form-check nd-form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
              value="1" <?= (!isset($old['is_active']) || (int)$old['is_active'] === 1 ? 'checked' : '') ?>>
            <label class="form-check-label text-dark fw-medium" for="is_active">
              Ativo no Portal
            </label>
          </div>
          <small class="text-muted d-block mt-2">
             Se desmarcado, o documento ficará oculto para os usuários até ser ativado manualmente.
          </small>
      </div>
    </div>

    <div class="nd-card bg-light border-0">
      <div class="nd-card-body">
        <h6 class="nd-card-title mb-3 d-flex align-items-center gap-2">
           <i class="bi bi-info-circle text-primary"></i> Informações
        </h6>
        <ul class="nd-list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
           <li><strong>Tamanho Máx:</strong> 50 MB</li>
           <li><strong>Formatos:</strong> PDF, Office, Imagens e ZIP</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
const fileInput = document.getElementById('file');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

if (fileInput) {
    fileInput.addEventListener('change', function() {
      if (this.files && this.files.length > 0) {
        const file = this.files[0];
        fileName.textContent = file.name;
        
        let sizeText = '';
        const sizeMB = file.size / (1024 * 1024);
        if (sizeMB > 50) {
          sizeText = sizeMB.toFixed(2) + ' MB <span class="text-danger fw-bold">(Excede 50MB)</span>';
        } else {
          sizeText = sizeMB.toFixed(2) + ' MB';
        }
        fileSize.innerHTML = sizeText;
        fileInfo.style.display = 'block';
      } else {
        fileInfo.style.display = 'none';
      }
    });
}
</script>
