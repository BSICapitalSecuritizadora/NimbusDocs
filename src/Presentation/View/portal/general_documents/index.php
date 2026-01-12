<?php
/**
 * @var array $categories
 * @var array $documents
 * @var int|null $currentCategory
 * @var string $term
 */
?>
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-3 fw-bold text-dark">Biblioteca de Arquivos</h1>
        <p class="text-secondary">Encontre manuais, políticas e documentos importantes.</p>
    </div>
</div>

<div class="row">
    <!-- Sidebar / Filters -->
    <div class="col-lg-3 mb-4">
        <div class="nd-card">
            <div class="nd-card-body">
                <form action="/portal/documents/general" method="get">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Palavras-chave..." value="<?= htmlspecialchars($term ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Categorias</label>
                        <div class="d-flex flex-column gap-1">
                            <a href="/portal/documents/general" class="btn btn-sm text-start <?= empty($currentCategory) ? 'btn-primary' : 'btn-light text-dark' ?>">
                                <i class="bi bi-grid-fill me-2 opacity-50"></i> Tudo
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <a href="/portal/documents/general?category_id=<?= $cat['id'] ?>" 
                                   class="btn btn-sm text-start <?= ($currentCategory == $cat['id']) ? 'btn-primary' : 'btn-light text-dark' ?>">
                                    <i class="bi bi-folder-fill me-2 opacity-50"></i> <?= htmlspecialchars($cat['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($term)): ?>
                        <div class="mt-3">
                            <a href="/portal/documents/general" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-2"></i> Limpar filtros
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
            <div class="nd-card text-center py-5">
                <div class="nd-card-body">
                    <div class="mb-3">
                        <i class="bi bi-folder2-open display-4 text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-normal text-muted">Nenhum documento encontrado</h5>
                    <?php if (!empty($term) || !empty($currentCategory)): ?>
                        <p class="small text-secondary">Tente buscar por outros termos.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($documents as $doc): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="nd-card h-100 position-relative hover-shadow transition-all">
                            <div class="nd-card-body d-flex flex-column">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="rounded-3 bg-primary-subtle p-2 text-primary">
                                        <i class="bi bi-file-earmark-text fs-4"></i>
                                    </div>
                                    <?php if (!empty($doc['category_name'])): ?>
                                        <span class="badge bg-light text-secondary border fw-normal">
                                            <?= htmlspecialchars($doc['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title h6 text-dark fw-bold mb-2">
                                    <a href="/portal/documents/general/<?= $doc['id'] ?>" class="text-decoration-none text-dark stretched-link">
                                        <?= htmlspecialchars($doc['title']) ?>
                                    </a>
                                </h5>
                                
                                <p class="card-text small text-secondary mb-3 text-truncate-2">
                                    <?= htmlspecialchars($doc['description'] ?? '') ?>
                                </p>
                                
                                <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top border-light-subtle">
                                    <span class="small text-muted">
                                        <?= date('d/m/Y', strtotime($doc['created_at'])) ?>
                                    </span>
                                    <div class="btn btn-sm btn-icon btn-light text-primary rounded-circle">
                                        <i class="bi bi-download"></i>
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
