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
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-file-earmark-text text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Consulta Documental</h1>
            <p class="text-muted mb-0 small">Protocolo #<?= (int)$document['id'] ?></p>
        </div>
    </div>
    <a href="/admin/documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar à Listagem
    </a>
</div>

<div class="row">
    <!-- Document Info Card -->
    <div class="col-lg-8">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados do Arquivo</h5>
            </div>
            <div class="nd-card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="nd-label">Assunto/Identificação</label>
                        <div class="p-3 bg-light rounded">
                            <?= htmlspecialchars($document['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <?php if (!empty($document['description'])): ?>
                    <div class="col-md-12">
                        <label class="nd-label">Observações</label>
                        <div class="p-3 bg-light rounded">
                            <?= nl2br(htmlspecialchars($document['description'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label class="nd-label">Arquivo Original</label>
                        <div class="p-3 bg-light rounded font-monospace small">
                            <?= htmlspecialchars($document['file_original_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label">Dimensão</label>
                        <div class="p-3 bg-light rounded">
                            <?php
                            $size = (int)($document['file_size'] ?? 0);
                            if ($size >= 1048576) {
                                echo number_format($size / 1048576, 2) . ' MB';
                            } elseif ($size >= 1024) {
                                echo number_format($size / 1024, 2) . ' KB';
                            } else {
                                echo $size . ' bytes';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label">Tipo MIME</label>
                        <div class="p-3 bg-light rounded font-monospace small">
                            <?= htmlspecialchars($document['file_mime'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label">Data de Registro</label>
                        <div class="p-3 bg-light rounded">
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($document['created_at'])) ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>

                <!-- Download Button -->
                <?php if (!empty($document['file_path']) && is_file($document['file_path'])): ?>
                <div class="mt-4 pt-3 border-top">
                    <a href="/admin/files/<?= (int)$document['id'] ?>/download" class="nd-btn nd-btn-gold">
                        <i class="bi bi-download me-1"></i>
                        Baixar Cópia Digital
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- User Info Sidebar -->
    <div class="col-lg-4">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Titular Vinculado</h5>
            </div>
            <div class="nd-card-body">
                <?php if ($user): ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="nd-avatar nd-avatar-lg nd-avatar-initials" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($user['full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>

                <hr>

                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Situação Cadastral:</span>
                        <?php
                        $status = $user['status'] ?? 'ACTIVE';
                        $statusLabels = [
                            'ACTIVE' => 'Ativo',
                            'INACTIVE' => 'Inativo',
                            'INVITED' => 'Convidado',
                            'BLOCKED' => 'Bloqueado',
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
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID:</span>
                        <span class="fw-medium">#<?= (int)$user['id'] ?></span>
                    </div>
                </div>

                <a href="/admin/portal-users/<?= (int)$user['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm w-100 mt-3">
                    <i class="bi bi-person-lines-fill me-1"></i>
                    Acessar Perfil
                </a>
                <?php else: ?>
                <div class="text-center py-3">
                    <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">Titular não localizado</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-gear" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Controles</h5>
            </div>
            <div class="nd-card-body">
                <form method="post" action="/admin/documents/<?= (int)$document['id'] ?>/delete" 
                      onsubmit="return confirm('Confirma a exclusão definitiva deste documento?');">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="nd-btn nd-btn-outline text-danger w-100">
                        <i class="bi bi-trash me-1"></i>
                        Excluir Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
