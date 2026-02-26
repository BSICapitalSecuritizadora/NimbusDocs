<?php
/**
 * @var array $categories
 * @var array $documents
 * @var int|null $currentCategory
 * @var string $term
 */
?>
<!-- Page Header -->
<div class="row align-items-center mb-5">
    <div class="col-12">
        <h1 class="h3 fw-bold text-dark mb-1">Biblioteca de Arquivos</h1>
        <p class="text-secondary mb-0">Manuais, políticas e documentos oficiais para download.</p>
    </div>
</div>

<div class="row g-5">
    <!-- Sidebar / Filters -->
    <div class="col-lg-3">
        <div class="sticky-top" style="top: 2rem; z-index: 10;">
            <form action="/portal/documents" method="get">
                <!-- Search -->
                <div class="mb-5">
                    <label class="form-label small fw-bold text-uppercase text-secondary ls-1 mb-2">Buscar</label>
                    <div class="nd-input-group position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted opacity-50"></i>
                        <input type="text" name="q" class="form-control border-0 bg-white shadow-sm ps-5 py-3 rounded-3" 
                               placeholder="Palavras-chave..." 
                               value="<?= htmlspecialchars($term ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                
                <!-- Categories -->
                <div>
                    <label class="form-label small fw-bold text-uppercase text-secondary ls-1 mb-3">Categorias</label>
                    <div class="d-flex flex-column gap-2">
                        <a href="/portal/documents" 
                           class="btn text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between transition-fast border-0
                           <?= empty($currentCategory) ? 'bg-primary text-white shadow-sm fw-medium' : 'bg-white text-secondary shadow-sm hover-shadow' ?>">
                            <span><i class="bi bi-grid me-2 <?= empty($currentCategory) ? '' : 'opacity-50' ?>"></i> Todas</span>
                            <?php if(empty($currentCategory)): ?>
                                <i class="bi bi-check-lg small"></i>
                            <?php endif; ?>
                        </a>
                        
                        <?php foreach ($categories as $cat): ?>
                            <?php $isActive = ($currentCategory == $cat['id']); ?>
                            <a href="/portal/documents?category_id=<?= $cat['id'] ?>" 
                               class="btn text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between transition-fast border-0
                               <?= $isActive ? 'bg-primary text-white shadow-sm fw-medium' : 'bg-white text-secondary shadow-sm hover-shadow' ?>">
                                <span class="text-truncate">
                                    <i class="bi bi-folder2-open me-2 <?= $isActive ? '' : 'opacity-50' ?>"></i> 
                                    <?= htmlspecialchars($cat['name']) ?>
                                </span>
                                <?php if($isActive): ?>
                                    <i class="bi bi-check-lg small"></i>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (!empty($term) || !empty($currentCategory)): ?>
                    <div class="mt-4 pt-4 border-top border-light-subtle">
                        <a href="/portal/documents" class="btn btn-sm btn-outline-danger w-100 border-0 bg-danger-subtle text-danger rounded-3 py-2">
                            <i class="bi bi-x-lg me-1"></i> Limpar filtros
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="col-lg-9">
        
        <!-- Tabs -->
        <ul class="nav nav-pills mb-4 gap-2" id="docsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 fw-medium flex-fill" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                    <i class="bi bi-building me-2"></i> Documentos Gerais
                    <?php if (!empty($documents)): ?>
                        <span class="badge bg-light text-dark ms-2 rounded-pill"><?= count($documents) ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-medium flex-fill" id="private-tab" data-bs-toggle="pill" data-bs-target="#private" type="button" role="tab" aria-controls="private" aria-selected="false">
                    <i class="bi bi-person-lock me-2"></i> Meus Documentos
                    <?php if (!empty($userDocs)): ?>
                        <span class="badge bg-light text-dark ms-2 rounded-pill"><?= count($userDocs) ?></span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="docsTabsContent">
            <!-- General Documents Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <?php if (empty($documents)): ?>
                    <div class="nd-card border-0 shadow-sm text-center py-5 rounded-4">
                        <div class="nd-card-body">
                            <div class="mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border p-4">
                                    <i class="bi bi-folder-x text-muted opacity-25 display-4"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Nenhum documento encontrado</h5>
                            <p class="text-secondary small mb-0 w-75 mx-auto">Não encontramos arquivos com os filtros atuais. Tente buscar por outros termos ou categorias.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($documents as $doc): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift transition-fast position-relative group-hover bg-white overflow-hidden">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex align-items-start justify-content-between mb-4">
                                            <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 56px;">
                                                <i class="bi bi-file-earmark-text-fill fs-4"></i>
                                            </div>
                                            <?php if (!empty($doc['category_name'])): ?>
                                                <span class="badge bg-light text-secondary border fw-medium px-2 py-1 rounded-pill small">
                                                    <?= htmlspecialchars($doc['category_name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h5 class="card-title h6 text-dark fw-bold mb-2">
                                            <a href="#" onclick="openPreview(<?= $doc['id'] ?>, '<?= htmlspecialchars($doc['title'], ENT_QUOTES) ?>'); return false;" class="text-decoration-none text-dark stretched-link">
                                                <?= htmlspecialchars($doc['title']) ?>
                                            </a>
                                        </h5>
                                        
                                        <p class="card-text x-small text-secondary mb-4 text-truncate-2 flex-grow-1 opacity-75">
                                            <?= htmlspecialchars($doc['description'] ?? 'Sem descrição disponível.') ?>
                                        </p>
                                        
                                        <div class="mt-auto d-flex align-items-end justify-content-between pt-3 border-top border-light-subtle w-100">
                                            <div class="d-flex align-items-center gap-2 text-muted x-small fw-medium">
                                                <i class="bi bi-calendar3"></i>
                                                <?= date('d/m/Y', strtotime($doc['created_at'])) ?>
                                            </div>
                                            
                                            <div class="d-flex gap-1 position-relative" style="z-index: 2;">
                                                <button type="button" 
                                                        class="btn btn-sm btn-light text-primary hover-primary border rounded-circle d-flex align-items-center justify-content-center shadow-sm transition-fast" 
                                                        style="width: 34px; height: 34px;"
                                                        onclick="openPreview(<?= $doc['id'] ?>, '<?= htmlspecialchars($doc['title'], ENT_QUOTES) ?>')"
                                                        title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="/portal/documents/general/<?= $doc['id'] ?>" 
                                                class="btn btn-sm btn-light text-secondary hover-dark border rounded-circle d-flex align-items-center justify-content-center shadow-sm transition-fast"
                                                style="width: 34px; height: 34px;" 
                                                title="Baixar">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Private User Documents Tab -->
            <div class="tab-pane fade" id="private" role="tabpanel" aria-labelledby="private-tab">
                <?php if (empty($userDocs)): ?>
                    <div class="nd-card border-0 shadow-sm text-center py-5 rounded-4">
                        <div class="nd-card-body">
                            <div class="mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border p-4">
                                    <i class="bi bi-person-fill-lock text-muted opacity-25 display-4"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Nenhum documento privado</h5>
                            <p class="text-secondary small mb-0 w-75 mx-auto">Você não possui documentos exclusivos vinculados à sua conta no momento.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($userDocs as $doc): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift transition-fast position-relative group-hover bg-white overflow-hidden" style="border-top: 3px solid var(--nd-gold-500) !important;">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex align-items-start justify-content-between mb-4">
                                            <div class="rounded-3 bg-dark text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 56px;">
                                                <i class="bi bi-shield-lock-fill fs-4"></i>
                                            </div>
                                            <span class="badge bg-gold text-dark border fw-medium px-2 py-1 rounded-pill small">
                                                Exclusivo
                                            </span>
                                        </div>
                                        
                                        <h5 class="card-title h6 text-dark fw-bold mb-2">
                                            <?= htmlspecialchars($doc['title']) ?>
                                        </h5>
                                        
                                        <p class="card-text x-small text-secondary mb-4 text-truncate-2 flex-grow-1 opacity-75">
                                            <?= htmlspecialchars($doc['description'] ?? 'Documento privado.') ?>
                                        </p>
                                        
                                        <div class="mt-auto d-flex align-items-end justify-content-between pt-3 border-top border-light-subtle w-100">
                                            <div class="d-flex align-items-center gap-2 text-muted x-small fw-medium">
                                                <i class="bi bi-calendar3"></i>
                                                <?= date('d/m/Y', strtotime($doc['created_at'])) ?>
                                            </div>
                                            
                                            <div class="d-flex gap-1 position-relative" style="z-index: 2;">
                                                <button type="button" 
                                                        class="btn btn-sm btn-light text-dark hover-dark border rounded-circle d-flex align-items-center justify-content-center shadow-sm transition-fast" 
                                                        style="width: 34px; height: 34px;"
                                                        onclick="openPreview(<?= $doc['id'] ?>, '<?= htmlspecialchars($doc['title'], ENT_QUOTES) ?>', '/portal/documents')"
                                                        title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="/portal/documents/<?= $doc['id'] ?>" 
                                                class="btn btn-sm btn-light text-dark hover-dark border rounded-circle d-flex align-items-center justify-content-center shadow-sm transition-fast"
                                                style="width: 34px; height: 34px;" 
                                                title="Baixar">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="height: 90vh;">
        <div class="modal-content h-100 border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold mb-0" id="previewModalLabel">Visualizar Documento</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="downloadBtn" class="btn btn-sm btn-primary rounded-pill px-3" download>
                        <i class="bi bi-download me-1"></i> Baixar
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-light position-relative">
                <div id="loader" class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div class="small text-muted">Carregando visualização...</div>
                </div>
                <iframe src="" id="previewFrame" class="w-100 h-100 border-0" onload="document.getElementById('loader').style.display='none'"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function openPreview(id, title, baseUrl = '/portal/documents/general') {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const frame = document.getElementById('previewFrame');
    const label = document.getElementById('previewModalLabel');
    const download = document.getElementById('downloadBtn');
    const loader = document.getElementById('loader');
    
    // Reset state
    frame.src = 'about:blank';
    loader.style.display = 'block';
    
    // Set Data
    label.textContent = title;
    const url = baseUrl + '/' + id;
    
    frame.src = url + '?preview=1';
    download.href = url;
    
    modal.show();
}
</script>
