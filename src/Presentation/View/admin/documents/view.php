<?php
/**
 * Página de visualização/detalhes de um documento do portal (admin view)
 * Espera em $viewData:
 * - array $document
 * - array $userInfo
 * - string $csrfToken
 * - array $flash (success, error)
 */
$document = $viewData['document'] ?? [];
$userInfo = $viewData['userInfo'] ?? [];
$csrfToken = $viewData['csrfToken'] ?? '';
$flash = $viewData['flash'] ?? [];

$docId = (int)($document['id'] ?? 0);
if (!$docId) {
  http_response_code(404);
  echo 'Documento não encontrado.';
  exit;
}
?>

<div class="mb-4">
  <a href="/admin/documents" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-chevron-left"></i> Voltar
  </a>
  <h1 class="h4">Detalhes do Documento</h1>
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
  <!-- Coluna Principal -->
  <div class="col-lg-8">
    <!-- Informações do Documento -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Informações do Documento</h6>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-8">
            <h5><?= htmlspecialchars($document['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
            <?php if (!empty($document['description'])): ?>
              <p class="text-muted">
                <?= htmlspecialchars($document['description'], ENT_QUOTES, 'UTF-8') ?>
              </p>
            <?php endif; ?>
          </div>
          <div class="col-md-4 text-md-end">
            <span class="badge bg-info">
              ID #<?= (int)$document['id'] ?>
            </span>
          </div>
        </div>

        <hr>

        <div class="row g-4 small">
          <div class="col-md-6">
            <div class="mb-3">
              <small class="text-muted d-block">Usuário</small>
              <strong><?= htmlspecialchars($document['user_full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></strong>
              <br>
              <small class="text-muted"><?= htmlspecialchars($document['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>

            <div class="mb-3">
              <small class="text-muted d-block">Criado em</small>
              <small><?= htmlspecialchars($document['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <small class="text-muted d-block">Nome do Arquivo</small>
              <small class="text-monospace"><?= htmlspecialchars($document['file_original_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>

            <div class="mb-3">
              <small class="text-muted d-block">Tipo MIME</small>
              <small><?= htmlspecialchars($document['file_mime'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Arquivo -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Arquivo</h6>
      </div>
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <div class="mb-2">
              <small class="text-muted d-block">Nome</small>
              <strong class="text-monospace"><?= htmlspecialchars($document['file_original_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <div class="mb-2">
              <small class="text-muted d-block">Tamanho</small>
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
            <div class="mb-2">
              <small class="text-muted d-block">Caminho</small>
              <small class="text-monospace text-break"><?= htmlspecialchars($document['file_path'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            </div>
          </div>
          <div class="col-auto">
            <?php if (!empty($document['file_path'])): ?>
              <a href="<?= htmlspecialchars($document['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
                class="btn btn-outline-primary" download title="Download do arquivo">
                <i class="bi bi-download"></i> Download
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Ações -->
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Ações</h6>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2 d-md-flex">
          <form method="post" action="/admin/documents/<?= $docId ?>/delete" class="flex-fill" onsubmit="return confirm('Tem certeza que deseja deletar este documento?');">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-danger w-100">
              <i class="bi bi-trash"></i> Deletar documento
            </button>
          </form>
        </div>
        <small class="text-muted d-block mt-3">
          <i class="bi bi-info-circle"></i>
          Esta ação é irreversível e removerá o arquivo permanentemente.
        </small>
      </div>
    </div>
  </div>

  <!-- Painel Lateral -->
  <div class="col-lg-4">
    <!-- Informações do Usuário -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Informações do Usuário</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <small class="text-muted d-block">Nome Completo</small>
          <strong><?= htmlspecialchars($document['user_full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="mb-3">
          <small class="text-muted d-block">Email</small>
          <small>
            <a href="mailto:<?= htmlspecialchars($document['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($document['user_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
            </a>
          </small>
        </div>

        <div class="mb-3">
          <small class="text-muted d-block">Documento</small>
          <small><?= htmlspecialchars($document['user_document_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></small>
        </div>

        <div class="mb-3">
          <small class="text-muted d-block">Telefone</small>
          <small><?= htmlspecialchars($document['user_phone_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></small>
        </div>

        <hr>

        <a href="/admin/portal-users/<?= (int)($document['portal_user_id'] ?? 0) ?>" class="btn btn-sm btn-outline-primary w-100">
          <i class="bi bi-person"></i> Ver Usuário
        </a>
      </div>
    </div>

    <!-- Metadados -->
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">Metadados</h6>
      </div>
      <div class="card-body small">
        <div class="mb-3">
          <small class="text-muted d-block">ID Documento</small>
          <code>#<?= (int)$document['id'] ?></code>
        </div>

        <div class="mb-3">
          <small class="text-muted d-block">ID Usuário</small>
          <code>#<?= (int)($document['portal_user_id'] ?? 0) ?></code>
        </div>

        <div class="mb-3">
          <small class="text-muted d-block">Criado em</small>
          <small><?= htmlspecialchars($document['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
        </div>

        <div class="mb-3">
          <small class="text-muted d-block">Admin criador</small>
          <small><?= !empty($document['created_by_admin']) ? 'Sim (#' . (int)$document['created_by_admin'] . ')' : 'Não' ?></small>
        </div>

        <div>
          <small class="text-muted d-block">Checksum</small>
          <small class="text-monospace text-break"><?= htmlspecialchars($document['checksum'] ?? '—', ENT_QUOTES, 'UTF-8') ?></small>
        </div>
      </div>
    </div>
  </div>
</div>
