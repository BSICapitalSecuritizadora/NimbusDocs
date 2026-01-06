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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
             <i class="bi bi-file-text"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes do Documento</h1>
            <p class="text-muted mb-0 small">#<?= $docId ?> &bull; Visualização de detalhes</p>
        </div>
    </div>
    <a href="/admin/documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
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
  <!-- Coluna Principal -->
  <div class="col-lg-8">
    <!-- Informações do Documento -->
    <div class="nd-card mb-4">
      <div class="nd-card-header d-flex align-items-center gap-2">
         <div class="nd-avatar nd-avatar-sm" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
            <i class="bi bi-file-text"></i>
         </div>
         <h5 class="nd-card-title mb-0">Informações do Documento</h5>
      </div>
      <div class="nd-card-body">
         <h4 class="h5 fw-bold text-dark mb-2"><?= htmlspecialchars($document['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h4>
         <?php if (!empty($document['description'])): ?>
            <p class="text-muted mb-4 p-3 bg-light rounded border border-light">
                <?= nl2br(htmlspecialchars($document['description'], ENT_QUOTES, 'UTF-8')) ?>
            </p>
         <?php else: ?>
            <p class="text-muted fst-italic mb-4">Sem descrição disponível.</p>
         <?php endif; ?>

         <div class="d-flex gap-4 border-top pt-3">
             <div>
                 <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Criado em</small>
                 <span class="text-dark fw-medium"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($document['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?></span>
             </div>
             <div>
                 <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Tipo</small>
                 <span class="text-dark font-monospace"><?= htmlspecialchars($document['file_mime'] ?? 'UNK', ENT_QUOTES, 'UTF-8') ?></span>
             </div>
         </div>
      </div>
    </div>

    <!-- Arquivo -->
    <div class="nd-card mb-4">
      <div class="nd-card-header">
         <h6 class="nd-card-title mb-0">Arquivo Anexado</h6>
      </div>
      <div class="nd-card-body">
        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded border border-light mb-3">
             <div class="d-flex align-items-center gap-3 overflow-hidden">
                 <div class="nd-avatar bg-white border">
                    <i class="bi bi-paperclip text-muted"></i>
                 </div>
                 <div class="overflow-hidden">
                     <div class="fw-bold text-dark text-truncate" style="max-width: 300px;">
                        <?= htmlspecialchars($document['file_original_name'] ?? 'arquivo', ENT_QUOTES, 'UTF-8') ?>
                     </div>
                     <div class="small text-muted">
                        <?php
                            $bytes = (int)($document['file_size'] ?? 0);
                            if ($bytes < 1024) echo $bytes . ' B';
                            elseif ($bytes < 1024 * 1024) echo round($bytes / 1024, 2) . ' KB';
                            else echo round($bytes / (1024 * 1024), 2) . ' MB';
                        ?>
                     </div>
                 </div>
             </div>
             
            <?php if (!empty($document['file_path'])): ?>
              <a href="<?= htmlspecialchars($document['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
                class="nd-btn nd-btn-primary nd-btn-sm" 
                download>
                <i class="bi bi-download me-1"></i> Baixar
              </a>
            <?php endif; ?>
        </div>
        
        <div class="small text-muted">
            <i class="bi bi-hdd-network me-1"></i>
            Caminho do sistema: <span class="font-monospace user-select-all"><?= htmlspecialchars($document['file_path'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
      </div>
    </div>

    <!-- Ações -->
    <div class="nd-card mb-4">
      <div class="nd-card-header">
        <h6 class="nd-card-title mb-0 text-danger">Zona de Perigo</h6>
      </div>
      <div class="nd-card-body">
        <p class="small text-muted mb-3">
             A exclusão deste documento é permanente e removerá o acesso do usuário ao arquivo.
        </p>
        <form method="post" action="/admin/documents/<?= $docId ?>/delete" onsubmit="return confirm('Tem certeza que deseja deletar este documento? Ação irreversível.');">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="nd-btn w-100 bg-danger text-white border-0 hover-opacity-90">
              <i class="bi bi-trash me-1"></i> Excluir Documento Permanentemente
            </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Painel Lateral -->
  <div class="col-lg-4">
    <!-- Informações do Usuário -->
    <div class="nd-card mb-4">
      <div class="nd-card-header">
        <h6 class="nd-card-title mb-0">Usuário do Portal</h6>
      </div>
      <div class="nd-card-body">
         <div class="d-flex align-items-center gap-3 mb-4">
             <div class="nd-avatar nd-avatar-lg nd-avatar-initials bg-light text-primary border border-primary-subtle">
                <?= strtoupper(substr($document['user_full_name'] ?? 'U', 0, 1)) ?>
             </div>
             <div>
                 <div class="fw-bold text-dark"><?= htmlspecialchars($document['user_full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                 <div class="small text-muted">ID: #<?= (int)($document['portal_user_id'] ?? 0) ?></div>
             </div>
         </div>

         <ul class="nd-list-unstyled small d-flex flex-column gap-3 mb-4">
             <li>
                 <span class="d-block text-muted text-uppercase" style="font-size:0.7rem;">Email</span>
                 <a href="mailto:<?= htmlspecialchars($document['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none fw-medium">
                    <?= htmlspecialchars($document['user_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                 </a>
             </li>
             <li>
                 <span class="d-block text-muted text-uppercase" style="font-size:0.7rem;">Documento (CPF/CNPJ)</span>
                 <span class="text-dark fw-medium"><?= htmlspecialchars($document['user_document_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
             </li>
             <li>
                 <span class="d-block text-muted text-uppercase" style="font-size:0.7rem;">Telefone</span>
                 <span class="text-dark fw-medium"><?= htmlspecialchars($document['user_phone_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
             </li>
         </ul>

         <a href="/admin/portal-users/<?= (int)($document['portal_user_id'] ?? 0) ?>" class="nd-btn nd-btn-outline w-100">
             <i class="bi bi-person-badge me-1"></i> Ver Perfil Completo
         </a>
      </div>
    </div>

    <!-- Metadados -->
    <div class="nd-card bg-light border-0">
      <div class="nd-card-body">
        <h6 class="nd-card-title mb-3 text-muted small text-uppercase">Metadados Técnicos</h6>
        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
            <li class="d-flex justify-content-between text-muted">
                <span>ID Sistema:</span>
                <span class="font-monospace text-dark">#<?= (int)$document['id'] ?></span>
            </li>
            <li class="d-flex justify-content-between text-muted">
                <span>Criado por Admin:</span>
                <span class="text-dark"><?= !empty($document['created_by_admin']) ? 'Sim (#' . (int)$document['created_by_admin'] . ')' : 'Não' ?></span>
            </li>
            <li class="text-muted">
                <span class="d-block mb-1">Checksum:</span>
                <span class="font-monospace text-break bg-white px-1 border rounded" style="font-size: 0.75rem;">
                    <?= htmlspecialchars($document['checksum'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </li>
        </ul>
      </div>
    </div>
  </div>
</div>
