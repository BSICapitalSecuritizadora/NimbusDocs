<?php
/**
 * Página de visualização de documentos do portal
 * Espera em $viewData:
 * - array $categories
 * - array $documents
 * - string $searchTerm (opcional)
 * - int $selectedCategoryId (opcional)
 * - string $csrfToken
 * - array $userDocs (documentos do usuário, opcional)
 */
$categories = $viewData['categories'] ?? [];
$documents = $viewData['documents'] ?? [];
$userDocs = $viewData['userDocs'] ?? [];
$searchTerm = $viewData['searchTerm'] ?? '';
$selectedCategoryId = $viewData['selectedCategoryId'] ?? null;
$csrfToken = $viewData['csrfToken'] ?? '';
?>

<div class="mb-4">
  <h1 class="h4 mb-3">Biblioteca</h1>
  <p class="text-muted">Encontre manuais, políticas e arquivos importantes.</p>
</div>

<!-- Barra de Filtro -->
<div class="card mb-4">
  <div class="card-body">
    <form method="get" action="/portal/documents" class="row g-3">
      <!-- Busca -->
      <div class="col-md-6">
        <label for="searchTerm" class="form-label">Buscar</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" id="searchTerm" name="search" 
            placeholder="Digite palavras-chave..." 
            value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <!-- Filtro por Categoria -->
      <div class="col-md-4">
        <label for="categoryId" class="form-label">Categorias</label>
        <select class="form-select" id="categoryId" name="category">
          <option value="">Tudo</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= ((int)$selectedCategoryId === (int)$cat['id'] ? 'selected' : '') ?>>
              <?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Botão Filtrar -->
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-funnel"></i> Filtrar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Abas: Documentos Gerais e Meus Documentos -->
<ul class="nav nav-tabs mb-4" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="generalTab" data-bs-toggle="tab" 
      data-bs-target="#generalTabContent" type="button" role="tab" 
      aria-controls="generalTabContent" aria-selected="true">
      <i class="bi bi-files"></i> Biblioteca
      <?php if (!empty($documents)): ?>
        <span class="badge bg-secondary ms-2"><?= count($documents) ?></span>
      <?php endif; ?>
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="myDocsTab" data-bs-toggle="tab" 
      data-bs-target="#myDocsTabContent" type="button" role="tab" 
      aria-controls="myDocsTabContent" aria-selected="false">
      <i class="bi bi-file-earmark"></i> Seus Arquivos
      <?php if (!empty($userDocs)): ?>
        <span class="badge bg-secondary ms-2"><?= count($userDocs) ?></span>
      <?php endif; ?>
    </button>
  </li>
</ul>

<!-- Conteúdo das Abas -->
<div class="tab-content">
  <!-- Documentos Gerais -->
  <div class="tab-pane fade show active" id="generalTabContent" role="tabpanel" aria-labelledby="generalTab">
    <?php if (!$documents): ?>
      <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle"></i> 
        <?= !empty($searchTerm) || !empty($selectedCategoryId) 
          ? 'Nenhum arquivo localizado com os filtros aplicados.'
          : 'Nenhum documento disponível no acervo.' ?>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($documents as $doc): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <!-- Categoria Badge -->
                <div class="mb-2">
                  <span class="badge bg-primary">
                    <?= htmlspecialchars($doc['category_name'] ?? 'Sem categoria', ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </div>

                <!-- Título -->
                <h6 class="card-title">
                  <?= htmlspecialchars($doc['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h6>

                <!-- Descrição -->
                <?php if (!empty($doc['description'])): ?>
                  <p class="card-text text-muted small flex-grow-1">
                    <?= htmlspecialchars(
                      strlen($doc['description']) > 100 
                        ? substr($doc['description'], 0, 100) . '...' 
                        : $doc['description'],
                      ENT_QUOTES, 'UTF-8'
                    ) ?>
                  </p>
                <?php endif; ?>

                <!-- Metadados -->
                <div class="border-top pt-3 mt-3">
                  <div class="row g-2 small text-muted mb-3">
                    <div class="col-6">
                      <i class="bi bi-calendar"></i>
                      <small><?= htmlspecialchars($doc['published_at'] ?? 'Agora', ENT_QUOTES, 'UTF-8') ?></small>
                    </div>
                    <div class="col-6 text-end">
                      <i class="bi bi-file-pdf"></i>
                      <small>
                        <?php
                        $bytes = (int)($doc['file_size'] ?? 0);
                        if ($bytes < 1024) {
                          echo $bytes . ' B';
                        } elseif ($bytes < 1024 * 1024) {
                          echo round($bytes / 1024, 1) . ' KB';
                        } else {
                          echo round($bytes / (1024 * 1024), 1) . ' MB';
                        }
                        ?>
                      </small>
                    </div>
                  </div>

                  <!-- Botão Download -->
                  <a href="<?= htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
                    class="btn btn-sm btn-primary w-100" download>
                    <i class="bi bi-download"></i> Baixar
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Meus Documentos -->
  <div class="tab-pane fade" id="myDocsTabContent" role="tabpanel" aria-labelledby="myDocsTab">
    <?php if (!$userDocs): ?>
      <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle"></i> 
        Você ainda não possui arquivos pessoais vinculados à sua conta.
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Título</th>
              <th>Descrição</th>
              <th>Tamanho</th>
              <th>Data de Upload</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($userDocs as $doc): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($doc['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                </td>
                <td>
                  <small class="text-muted">
                    <?= htmlspecialchars(
                      !empty($doc['description']) 
                        ? (strlen($doc['description']) > 50 ? substr($doc['description'], 0, 50) . '...' : $doc['description'])
                        : '—',
                      ENT_QUOTES, 'UTF-8'
                    ) ?>
                  </small>
                </td>
                <td>
                  <small class="text-muted">
                    <?php
                    $bytes = (int)($doc['file_size'] ?? 0);
                    if ($bytes < 1024) {
                      echo $bytes . ' B';
                    } elseif ($bytes < 1024 * 1024) {
                      echo round($bytes / 1024, 1) . ' KB';
                    } else {
                      echo round($bytes / (1024 * 1024), 1) . ' MB';
                    }
                    ?>
                  </small>
                </td>
                <td>
                  <small class="text-muted"><?= htmlspecialchars($doc['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                </td>
                <td class="text-end">
                  <a href="<?= htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
                    class="btn btn-sm btn-outline-primary" download title="Download">
                    <i class="bi bi-download"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>


<!-- Seção de Categorias (alternativa visual) -->
<?php if (!empty($categories) && empty($searchTerm)): ?>
  <div class="mt-5 pt-4 border-top">
    <h5 class="mb-4">Navegar por Categorias</h5>
    <div class="row g-3">
      <?php foreach ($categories as $cat): ?>
        <div class="col-md-4">
          <a href="/portal/documents?category=<?= (int)$cat['id'] ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body text-center">
                <div class="mb-3">
                  <i class="bi bi-folder2-open fs-3 text-primary"></i>
                </div>
                <h6 class="card-title"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></h6>
                <?php if (!empty($cat['description'])): ?>
                  <p class="card-text small text-muted">
                    <?= htmlspecialchars($cat['description'], ENT_QUOTES, 'UTF-8') ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<style>
.card {
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}

.card:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
  transform: translateY(-2px);
}

.nav-tabs .nav-link {
  color: #495057;
  border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link:hover {
  border-bottom-color: #0d6efd;
}

.nav-tabs .nav-link.active {
  color: #0d6efd;
  border-bottom-color: #0d6efd;
  background-color: transparent;
}
</style>
