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

<div class="row g-4">
    <!-- Sidebar / Filters -->
    <div class="col-lg-3">
        <div class="nd-card border-0 shadow-sm sticky-top" style="top: 2rem; z-index: 10;">
            <div class="nd-card-body p-4">
                <form action="/portal/documents/general" method="get">
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted ls-1">Buscar</label>
                        <div class="nd-input-group">
                            <input type="text" name="q" class="nd-input bg-light border-0 ps-5" 
                                   placeholder="Palavras-chave..." 
                                   value="<?= htmlspecialchars($term ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-search nd-input-icon text-muted opacity-50"></i>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div>
                        <label class="form-label small fw-bold text-uppercase text-muted ls-1 mb-2">Categorias</label>
                        <div class="d-flex flex-column gap-1">
                            <a href="/portal/documents/general" 
                               class="btn btn-sm text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between text-decoration-none transition-fast
                               <?= empty($currentCategory) ? 'bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>">
                                <span><i class="bi bi-grid me-2 <?= empty($currentCategory) ? '' : 'opacity-50' ?>"></i> Todas</span>
                                <?php if(empty($currentCategory)): ?>
                                    <i class="bi bi-check-lg small"></i>
                                <?php endif; ?>
                            </a>
                            
                            <?php foreach ($categories as $cat): ?>
                                <?php $isActive = ($currentCategory == $cat['id']); ?>
                                <a href="/portal/documents/general?category_id=<?= $cat['id'] ?>" 
                                   class="btn btn-sm text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between text-decoration-none transition-fast
                                   <?= $isActive ? 'bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>">
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
                        <div class="mt-4 pt-4 border-top">
                            <a href="/portal/documents/general" class="btn btn-sm btn-outline-danger w-100 border-0 bg-danger-subtle text-danger">
                                <i class="bi bi-x-circle me-1"></i> Limpar filtros
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="col-lg-9">
        <?php if (empty($documents)): ?>
            <div class="nd-card border-0 shadow-sm text-center py-5">
                <div class="nd-card-body">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px;">
                            <i class="bi bi-folder-x text-muted opacity-25 display-6"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Nenhum documento encontrado</h5>
                    <p class="text-secondary small mb-0">Não encontramos arquivos com os filtros atuais.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($documents as $doc): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="nd-card border-0 shadow-sm h-100 position-relative hover-lift transition-fast group-hover">
                            <div class="nd-card-body p-4 d-flex flex-column h-100">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="rounded-3 bg-primary-subtle p-3 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-file-earmark-text-fill fs-4"></i>
                                    </div>
                                    <?php if (!empty($doc['category_name'])): ?>
                                        <span class="badge bg-light text-muted border fw-medium px-2 py-1 rounded-pill">
                                            <?= htmlspecialchars($doc['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title h6 text-dark fw-bold mb-2 pe-3">
                                    <a href="/portal/documents/general/<?= $doc['id'] ?>" class="text-decoration-none text-dark stretched-link">
                                        <?= htmlspecialchars($doc['title']) ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text x-small text-muted mb-4 text-truncate-2 flex-grow-1" style="line-height: 1.6;">
                                    <?= htmlspecialchars($doc['description'] ?? 'Sem descrição disponível.') ?>
                                </p>
                                
                                <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top border-light-subtle w-100">
                                    <div class="d-flex align-items-center gap-2 text-secondary x-small">
                                        <i class="bi bi-calendar3"></i>
                                        <?= date('d/m/Y', strtotime($doc['created_at'])) ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-light text-primary hover-primary border rounded-circle d-flex align-items-center justify-content-center" 
                                                style="width: 32px; height: 32px;"
                                                onclick="openPreview(<?= $doc['id'] ?>, '<?= htmlspecialchars($doc['title'], ENT_QUOTES) ?>')"
                                                title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="/portal/documents/general/<?= $doc['id'] ?>" 
                                           class="btn btn-sm btn-light text-secondary hover-dark border rounded-circle d-flex align-items-center justify-content-center"
                                           style="width: 32px; height: 32px;" 
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

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="height: 90vh;">
        <div class="modal-content h-100 border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold mb-0" id="previewModalLabel">Visualizar Documento</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="downloadBtn" class="btn btn-sm btn-outline-primary" download>
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
function openPreview(id, title) {
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
    const url = '/portal/documents/general/' + id;
    
    frame.src = url + '?preview=1';
    download.href = url;
    
    modal.show();
}
</script>
