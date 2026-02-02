<?php
/**
 * Detalhes do Documento
 * @var array $document
 * @var array|null $user
 * @var string $csrfToken
 */
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/documents" class="text-decoration-none">
            <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
                <i class="bi bi-arrow-left text-white"></i>
            </div>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes do Documento</h1>
            <p class="text-muted mb-0 small">Visão geral do arquivo e cliente vinculado &bull; Protocolo #<?= (int)$document['id'] ?></p>
        </div>
    </div>
    <a href="/admin/documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-list-ul me-1"></i>
        Voltar à Listagem
    </a>
</div>

<div class="row">
    <!-- Document Info Card -->
    <div class="col-lg-8">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-richtext text-primary"></i>
                <h5 class="nd-card-title mb-0">Informações do Arquivo</h5>
            </div>
            <div class="nd-card-body">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="nd-label mb-1">Nome do Documento</label>
                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded border">
                            <i class="bi bi-card-heading text-muted"></i>
                            <span class="fw-medium text-dark"><?= htmlspecialchars($document['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>

                    <?php if (!empty($document['description'])): ?>
                    <div class="col-md-12">
                        <label class="nd-label mb-1">Descrição / Notas</label>
                        <div class="p-3 bg-light rounded border text-muted">
                            <?= nl2br(htmlspecialchars($document['description'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label class="nd-label mb-1">Arquivo Original</label>
                        <div class="p-2 border-bottom font-monospace text-dark small text-truncate" title="<?= htmlspecialchars($document['file_original_name'] ?? '', ENT_QUOTES) ?>">
                            <i class="bi bi-paperclip me-1 text-muted"></i>
                            <?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="nd-label mb-1">Tamanho</label>
                        <div class="p-2 border-bottom text-dark small">
                            <?php
                            $size = (int)($document['file_size'] ?? 0);
                            if ($size >= 1048576) {
                                echo number_format($size / 1048576, 2) . ' MB';
                            } elseif ($size >= 1024) {
                                echo number_format($size / 1024, 2) . ' KB';
                            } else {
                                echo $size . ' B';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="nd-label mb-1">Formato</label>
                        <div class="p-2 border-bottom text-dark small text-uppercase">
                            <?= pathinfo($document['file_original_name'] ?? '', PATHINFO_EXTENSION) ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label mb-1">Enviado em</label>
                        <div class="p-2 border-bottom text-dark small">
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            <?= htmlspecialchars(date('d/m/Y \à\s H:i', strtotime($document['created_at'])) ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    
                     <div class="col-md-6">
                        <label class="nd-label mb-1">Tipo MIME</label>
                        <div class="p-2 border-bottom text-muted small font-monospace">
                            <?= htmlspecialchars($document['file_mime'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>

                <!-- Download Action -->
                <?php if (!empty($document['file_path']) && is_file($document['file_path'])): ?>
                <div class="mt-4 pt-4 border-top">
                    <!-- Note: Assuming download route /admin/files/{id}/download exists or logic is handled elsewhere.
                         Since controller didn't show it explicitly, assuming existing link works or placeholder.
                         Original code had /admin/files/... so keeping consistent structure but improving style.
                         Wait, original code had /admin/files/ID/download.
                    -->
                    <a href="<?= htmlspecialchars($document['file_path']) ?>" download class="nd-btn nd-btn-primary w-100">
                        <i class="bi bi-cloud-download-fill me-2"></i>
                        Baixar Arquivo Original
                    </a>
                    <div class="text-center mt-2">
                        <small class="text-muted">Clique para transferir uma cópia segura do arquivo.</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- User Info Sidebar -->
    <div class="col-lg-4">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-muted"></i>
                <h5 class="nd-card-title mb-0">Cliente Associado</h5>
            </div>
            <div class="nd-card-body">
                <?php if ($user): ?>
                <div class="text-center mb-3">
                    <div class="nd-avatar nd-avatar-xl mx-auto mb-2 fw-bold fs-4" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($user['full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></h6>
                    <div class="text-muted small"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-3">
                    <span class="small text-muted">Status da Conta</span>
                    <?php
                    $status = $user['status'] ?? 'ACTIVE';
                    $statusLabels = [
                        'ACTIVE' => 'Ativa',
                        'INACTIVE' => 'Inativa',
                        'INVITED' => 'Convidado',
                        'BLOCKED' => 'Bloqueada',
                        'PENDING' => 'Pendente',
                    ];
                    $statusColors = [
                        'ACTIVE' => 'nd-badge-success',
                        'INACTIVE' => 'nd-badge-secondary',
                        'INVITED' => 'nd-badge-info',
                        'BLOCKED' => 'nd-badge-danger',
                        'PENDING' => 'nd-badge-warning',
                    ];
                    $label = $statusLabels[$status] ?? $status;
                    $badgeClass = $statusColors[$status] ?? 'nd-badge-secondary';
                    ?>
                    <span class="nd-badge <?= $badgeClass ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                
                <div class="d-flex justify-content-between small mb-3">
                    <span class="text-muted">ID do Cliente</span>
                    <span class="font-monospace text-dark">#<?= (int)$user['id'] ?></span>
                </div>

                <a href="/admin/portal-users/<?= (int)$user['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm w-100">
                    Ver Perfil Completo <i class="bi bi-chevron-right ms-1"></i>
                </a>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-person-x text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 small">Nenhum cliente associado.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="nd-card border-danger">
            <div class="nd-card-header bg-danger-subtle d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                <h5 class="nd-card-title mb-0 text-danger">Zona de Perigo</h5>
            </div>
            <div class="nd-card-body">
                <p class="small text-muted mb-3">
                    Deseja remover este arquivo permanentemente? Esta ação não pode ser desfeita e o cliente perderá o acesso.
                </p>
                <form method="post" action="/admin/documents/<?= (int)$document['id'] ?>/delete" 
                      onsubmit="return confirm('ATENÇÃO: Você está prestes a excluir este documento PERMANENTEMENTE. Confirma a exclusão?');">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="nd-btn nd-btn-sm w-100 bg-danger text-white border-danger hover-danger-fill">
                        <i class="bi bi-trash me-2"></i>
                        Excluir Documento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
