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
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/general-documents" class="text-decoration-none">
            <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
                <i class="bi bi-arrow-left text-white"></i>
            </div>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Gerenciar Publicação</h1>
            <p class="text-muted mb-0 small">Protocolo #<?= $docId ?> &bull; Edição de dados e arquivo</p>
        </div>
    </div>
    <a href="/admin/general-documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-list-ul me-1"></i> Visualizar Listagem
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
  <!-- Main Column -->
  <div class="col-lg-8">
    <div class="nd-card mb-4">
      <div class="nd-card-header d-flex align-items-center gap-2">
         <i class="bi bi-pencil-square" style="color: var(--nd-gold-500);"></i>
         <h5 class="nd-card-title mb-0">Ficha Técnica</h5>
      </div>
      <div class="nd-card-body">
        <!-- Note: Added enctype just in case file upload is supported, though controller might not handle it yet based on reading. -->
        <form method="post" action="/admin/general-documents/<?= $docId ?>" novalidate enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="_method" value="PUT">

          <!-- Título -->
          <div class="mb-4">
            <label for="title" class="nd-label">Título da Publicação <span class="text-danger">*</span></label>
            <div class="nd-input-group">
                <input type="text" class="nd-input <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" 
                  id="title" name="title" 
                  value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  placeholder="Ex: Regulamento Interno 2024" 
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

           <!-- Substituir Arquivo (Visual Only logic if backend doesn't support, but good practice to show) 
                User note: Standard "Edit" often implies changing data. 
                If the backend update() doesn't handle files, this input will be ignored. 
                For now I'll leave it commented out or clearly marked if not sure. 
                But the user screenshot said "Edição de dados e arquivo", suggesting expected functionality.
                Looking at controller 'update' method (Step 1485):
                   $this->docs->update($id, ['category_id', 'title', 'description', 'is_active']);
                It DOES NOT update the file. So adding a file input here would differ from backend logic.
                I will skip adding the file input to avoid broken expectations, or better:
                Add a note or separate "Replace File" action if needed. 
                For now, I will stick to what works: metadata editing.
           -->

          <!-- Descrição -->
          <div class="mb-4">
            <label for="description" class="nd-label">Ementa/Descrição</label>
            <textarea class="nd-input w-100 <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
              id="description" name="description" rows="5" style="resize: none;"
              placeholder="Descreva o conteúdo e a finalidade deste documento..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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
            <a href="/admin/general-documents" class="nd-btn nd-btn-outline">
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
    <!-- Arquivo Atual -->
    <div class="nd-card mb-4">
      <div class="nd-card-header d-flex justify-content-between align-items-center">
        <h6 class="nd-card-title mb-0">Arquivo em Vigência</h6>
        <span class="badge bg-light text-dark border">v1.0</span>
      </div>
      <div class="nd-card-body">
        <?php
            $ext = strtolower(pathinfo($document['file_original_name'] ?? '', PATHINFO_EXTENSION));
            $iconParams = match($ext) {
                'pdf' => ['icon' => 'bi-file-earmark-pdf-fill', 'color' => 'var(--nd-danger-500)', 'bg' => 'var(--nd-danger-100)'],
                'doc', 'docx' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'var(--nd-primary-500)', 'bg' => 'var(--nd-primary-100)'],
                'xls', 'xlsx' => ['icon' => 'bi-file-earmark-excel-fill', 'color' => 'var(--nd-success-500)', 'bg' => 'var(--nd-success-100)'],
                default => ['icon' => 'bi-file-earmark-text-fill', 'color' => 'var(--nd-gray-600)', 'bg' => 'var(--nd-gray-100)']
            };
        ?>
        <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded border" style="background-color: #f8f9fa;">
             <div class="nd-avatar" style="background: <?= $iconParams['bg'] ?>; color: <?= $iconParams['color'] ?>;">
                <i class="bi <?= $iconParams['icon'] ?> fs-4"></i>
             </div>
             <div class="overflow-hidden">
                 <div class="fw-bold text-dark text-truncate" title="<?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>">
                     <?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                 </div>
                 <div class="small text-muted d-flex gap-2">
                    <span>
                    <?php
                    $bytes = (int)($document['file_size'] ?? 0);
                    if ($bytes < 1024) echo $bytes . ' B';
                    elseif ($bytes < 1024 * 1024) echo round($bytes / 1024, 2) . ' KB';
                    else echo round($bytes / (1024 * 1024), 2) . ' MB';
                    ?>
                    </span>
                    <span>&bull;</span>
                    <span class="text-uppercase"><?= $ext ?></span>
                 </div>
             </div>
        </div>
        
        <!-- TODO: Implement file replacement if backend allows -->
        <?php if (!empty($document['file_path'])): ?>
            <!-- Assuming public link access logic/route exists or file_path is accessible directly for download/view -->
            <!-- For admin usually a route like /admin/documents/{id}/download is better but using direct link for now as per previous code -->
             <a href="<?= htmlspecialchars($document['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
                class="nd-btn nd-btn-outline w-100" 
                target="_blank" download>
                <i class="bi bi-cloud-download me-2"></i> Baixar Cópia Digital
            </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Metadados -->
    <div class="nd-card bg-light border-0 mb-4">
      <div class="nd-card-body py-3">
        <h6 class="nd-card-title small text-muted mb-3 border-bottom pb-2">Metadados de Auditoria</h6>
        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
            <li class="d-flex justify-content-between text-muted">
                <span>Protocolo</span>
                <span class="font-monospace bg-white border px-2 py-0 rounded">#<?= (int)$document['id'] ?></span>
            </li>
             <li class="d-flex justify-content-between text-muted">
                <span>Data de Upload</span>
                <span class="text-dark fw-medium"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($document['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?></span>
            </li>
             <li class="d-flex justify-content-between text-muted">
                <span>Vigência Início</span>
                <span class="text-dark fw-medium"><?= htmlspecialchars(date('d/m/Y', strtotime($document['published_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <li class="d-flex justify-content-between text-muted">
                <span>Responsável</span>
                <span class="text-dark">Admin ID <?= (int)$document['created_by_admin'] ?></span>
            </li>
        </ul>
      </div>
    </div>

    <!-- Status e Ações -->
    <div class="nd-card mb-4">
      <div class="nd-card-header">
        <h6 class="nd-card-title mb-0">Visibilidade</h6>
      </div>
      <div class="nd-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
           <label class="nd-label mb-0 text-muted">Status</label>
            <?php if ((int)$document['is_active'] === 1): ?>
              <span class="nd-badge nd-badge-success">Publicado</span>
            <?php else: ?>
              <span class="nd-badge nd-badge-secondary">Arquivado</span>
            <?php endif; ?>
        </div>
        
        <form method="post" action="/admin/general-documents/<?= $docId ?>/toggle">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="nd-btn nd-btn-sm w-100 <?= (int)$document['is_active'] === 1 ? 'nd-btn-outline text-muted' : 'nd-btn-primary' ?>">
            <i class="bi <?= (int)$document['is_active'] === 1 ? 'bi-eye-slash' : 'bi-eye' ?> me-1"></i>
            <?= (int)$document['is_active'] === 1 ? 'Arquivar Documento' : 'Publicar no Portal' ?>
          </button>
        </form>
      </div>
    </div>

    <!-- Zona de Perigo -->
    <div class="nd-card border-danger">
        <div class="nd-card-header bg-danger text-white d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h5 class="nd-card-title mb-0 text-white">Zona de Perigo</h5>
        </div>
        <div class="nd-card-body">
            <p class="small text-muted mb-3">
                Remover permanentemente este arquivo impactará links externos que apontam para ele.
            </p>
            <form method="post" action="/admin/general-documents/<?= $docId ?>/delete" onsubmit="return confirm('Confirma a exclusão definitiva desta publicação? Ação irreversível.');">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="nd-btn nd-btn-sm w-100 bg-danger text-white border-danger hover-danger-fill">
                    <i class="bi bi-trash me-2"></i> Remover Definitivamente
                </button>
            </form>
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
