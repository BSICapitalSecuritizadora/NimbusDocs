<?php
/**
 * Espera em $viewData:
 * - array $documents
 * - array $categories
 * - string $csrfToken
 * - array $flash (success, error)
 * - array $errors (erros de validação)
 * - array $old (dados antigos do formulário)
 */
$documents = $viewData['documents'] ?? [];
$categories = $viewData['categories'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];
$errors = $viewData['errors'] ?? [];
$old = $viewData['old'] ?? [];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-file-earmark-text-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Documentos Gerais</h1>
            <p class="text-muted mb-0 small">Disponibilize arquivos e documentos para os usuários do portal</p>
        </div>
    </div>
    <button class="nd-btn nd-btn-gold nd-btn-sm" data-bs-toggle="modal" data-bs-target="#createDocumentModal">
        <i class="bi bi-plus-lg me-1"></i>
        Novo documento
    </button>
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

<!-- Documents List -->
<div class="nd-card">
    <div class="nd-card-body p-0">
        <?php if (!$documents): ?>
            <div class="text-center py-5">
                <i class="bi bi-folder2-open text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-2">Nenhum documento cadastrado.</p>
                <button class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#createDocumentModal">
                    Criar o primeiro documento
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Categoria</th>
                            <th>Arquivo</th>
                            <th>Status/Publicação</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nd-avatar" style="width: 36px; height: 36px; background: var(--nd-gray-100); color: var(--nd-navy-600);">
                                            <i class="bi bi-file-text"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium text-dark"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if (!empty($doc['description'])): ?>
                                                <div class="small text-muted text-truncate" style="max-width: 200px;">
                                                    <?= htmlspecialchars($doc['description'], ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal">
                                        <?= htmlspecialchars($doc['category_name'] ?? 'Geral', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-dark font-monospace"><?= htmlspecialchars($doc['file_original_name'] ?? 'arquivo', ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="text-muted">
                                            <?php
                                            $bytes = (int)($doc['file_size'] ?? 0);
                                            if ($bytes < 1024) echo $bytes . ' B';
                                            elseif ($bytes < 1024 * 1024) echo round($bytes / 1024, 2) . ' KB';
                                            else echo round($bytes / (1024 * 1024), 2) . ' MB';
                                            ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div>
                                            <?php if ((int)$doc['is_active'] === 1): ?>
                                                <span class="nd-badge nd-badge-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="nd-badge nd-badge-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= htmlspecialchars($doc['published_at'] ?? 'Agora', ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#toggleModal"
                                            data-doc-id="<?= (int)$doc['id'] ?>"
                                            data-doc-active="<?= (int)$doc['is_active'] ?>"
                                            title="<?= (int)$doc['is_active'] === 1 ? 'Desativar' : 'Ativar' ?>">
                                            <i class="bi <?= (int)$doc['is_active'] === 1 ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                        </button>

                                        <!-- Nota: Edit link aponta para rota que o controller pode não ter (editForm aponta para form.php que não existe). 
                                             Se falhar, usuário reportará. Mantendo conforme original. -->
                                        <a href="/admin/general-documents/<?= (int)$doc['id'] ?>/edit"
                                            class="nd-btn nd-btn-outline nd-btn-sm"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button type="button" class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-doc-id="<?= (int)$doc['id'] ?>"
                                            data-doc-title="<?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Criar Novo Documento -->
<div class="modal fade" id="createDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Novo Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form method="post" action="/admin/general-documents" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="nd-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="nd-input w-100" name="title" required placeholder="Ex: Manual de Conduta" value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="nd-label">Categoria <span class="text-danger">*</span></label>
                            <select class="nd-input form-select w-100" name="category_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>" <?= ((int)($old['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="nd-label">Arquivo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
                            <div class="form-text small">Max: 50MB. PDF, Office, Imagens or ZIP.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="nd-label">Descrição</label>
                            <textarea class="nd-input w-100" name="description" rows="3" placeholder="Breve descrição do documento..."><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="nd-label">Data de Publicação</label>
                            <input type="date" class="nd-input w-100" name="published_at" value="<?= htmlspecialchars($old['published_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="form-text small">Deixe vazio para publicar agora.</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check nd-form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="createIsActive" name="is_active" value="1" <?= (!isset($old['is_active']) || (int)$old['is_active'] === 1 ? 'checked' : '') ?>>
                                <label class="form-check-label text-dark fw-medium" for="createIsActive">
                                    Visível no Portal
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="nd-btn nd-btn-outline" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="nd-btn nd-btn-primary">Criar Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Toggle Status -->
<div class="modal fade" id="toggleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Alterar Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
             <form method="post" id="toggleForm">
                <div class="modal-body py-0">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p id="toggleMessage" class="text-muted mb-0"></p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-primary nd-btn-sm">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-danger">Excluir Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="deleteForm">
                <div class="modal-body py-0">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-muted">
                        Tem certeza que deseja excluir o documento <strong id="deleteTitle" class="text-dark"></strong>?
                        <br><span class="text-danger small">Esta ação não pode ser desfeita.</span>
                    </p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-sm bg-danger text-white border-danger">Excluir Permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Logic Preserved
const toggleModal = document.getElementById('toggleModal');
const toggleForm = document.getElementById('toggleForm');
const toggleMessage = document.getElementById('toggleMessage');

toggleModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const docId = btn.dataset.docId;
  const isActive = parseInt(btn.dataset.docActive);
  
  toggleMessage.textContent = isActive === 1 
    ? 'Deseja desativar este documento? Ele deixará de ser visível no portal.'
    : 'Deseja ativar este documento? Ele ficará visível para os usuários.';
  
  toggleForm.action = `/admin/general-documents/${docId}/toggle`;
});

const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteTitle = document.getElementById('deleteTitle');

deleteModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const docId = btn.dataset.docId;
  const title = btn.dataset.docTitle;
  
  deleteTitle.textContent = title;
  deleteForm.action = `/admin/general-documents/${docId}/delete`;
});
</script>
