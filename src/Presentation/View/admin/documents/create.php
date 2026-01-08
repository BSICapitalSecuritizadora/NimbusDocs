<?php
/**
 * Espera:
 * - array $users (id, full_name, email)
 * - string $csrfToken
 */
$users = $users ?? [];
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
            <i class="bi bi-cloud-upload"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Upload de Documento</h1>
            <p class="text-muted mb-0 small">Arquivamento digital em nome de titular</p>
        </div>
    </div>
    <a href="/admin/documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retornar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <div class="nd-avatar nd-avatar-sm" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
                    <i class="bi bi-cloud-upload"></i>
                </div>
                <h5 class="nd-card-title mb-0">Dados do Carregamento</h5>
            </div>
            
            <div class="nd-card-body">
                <form action="/admin/documents" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- User Selection -->
                    <div class="mb-4">
                        <label class="nd-label">Titular do Documento <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <select name="portal_user_id" class="nd-input form-select" required style="padding-left: 2.5rem;">
                                <option value="">Selecione o titular...</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>">
                                        <?= htmlspecialchars($u['full_name'] ?? $u['email'] ?? ('#' . (int)$u['id']), ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="bi bi-person nd-input-icon"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label class="nd-label">Assunto/Identificação <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text" name="title" class="nd-input" required placeholder="Ex: Contrato de Prestação de Serviços - Dez/2024" style="padding-left: 2.5rem;">
                            <i class="bi bi-type-h1 nd-input-icon"></i>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="nd-label">Observações</label>
                        <textarea name="description" class="nd-input w-100" rows="4" placeholder="Adicione observações ou metadados relevantes sobre este arquivo..." style="resize: none;"></textarea>
                    </div>

                    <!-- File -->
                    <div class="mb-4">
                        <label class="nd-label">Anexo Digital <span class="text-danger">*</span></label>
                        <div class="nd-input p-1">
                             <input type="file" name="file" class="form-control border-0 bg-transparent" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="/admin/documents" class="nd-btn nd-btn-outline">Cancelar</a>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-send me-1"></i> Arquivar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="nd-card bg-light border-0">
            <div class="nd-card-body">
                <h6 class="nd-card-title mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i> Diretrizes
                </h6>
                <p class="small text-muted mb-3">
                    Esta funcionalidade permite o arquivamento manual de documentos no prontuário digital do titular.
                </p>
                <ul class="nd-list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                    <li><strong>Acesso:</strong> O titular terá acesso imediato para visualização e download.</li>
                    <li><strong>Auditoria:</strong> A ação será registrada em log de segurança.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
