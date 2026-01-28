<?php

/** @var array $branding */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-palette-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Branding e Identidade Visual</h1>
            <p class="text-muted mb-0 small">Personalize a aparência do sistema e do portal do cliente</p>
        </div>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form Column -->
    <div class="col-12 col-lg-7">
        <form method="post" action="/admin/settings/branding/save" id="brandingForm">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <!-- Identity Section -->
            <div class="nd-card mb-4 border-0 shadow-sm">
                <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-building text-primary"></i>
                    <h5 class="nd-card-title mb-0">Identidade da Aplicação</h5>
                </div>
                <div class="nd-card-body p-4">
                    <div class="mb-3">
                        <label class="nd-label">Nome da Aplicação</label>
                        <div class="nd-input-group">
                            <input type="text" name="app_name" id="inputAppName" class="nd-input bg-light border-0"
                                value="<?= htmlspecialchars($branding['app_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ex: NimbusDocs" style="padding-left: 2.5rem;">
                             <i class="bi bi-type nd-input-icon text-muted"></i>
                        </div>
                        <div class="form-text small mt-1">Nome exibido no título das abas e rodapés.</div>
                    </div>

                    <div class="mb-0">
                        <label class="nd-label">Subtítulo / Descrição</label>
                         <div class="nd-input-group">
                            <input type="text" name="app_subtitle" id="inputAppSubtitle" class="nd-input bg-light border-0"
                                value="<?= htmlspecialchars($branding['app_subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ex: Portal seguro de documentos" style="padding-left: 2.5rem;">
                            <i class="bi bi-chat-square-text nd-input-icon text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colors Section -->
            <div class="nd-card mb-4 border-0 shadow-sm">
                <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-palette text-warning"></i>
                    <h5 class="nd-card-title mb-0">Paleta de Cores</h5>
                </div>
                <div class="nd-card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="nd-label d-block mb-2">Cor Primária</label>
                            <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                                <input type="color" name="primary_color" id="inputPrimaryColor"
                                    class="form-control form-control-color border-0 p-0 rounded-circle shadow-sm"
                                    style="width: 40px; height: 40px;"
                                    value="<?= htmlspecialchars($branding['primary_color'] ?? '#00205b', ENT_QUOTES, 'UTF-8') ?>"
                                    title="Escolher cor primária">
                                <div>
                                    <div class="fw-bold text-dark small">Cor Principal</div>
                                    <div class="text-muted x-small">Navbars, botões e headers.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <label class="nd-label d-block mb-2">Cor de Destaque</label>
                            <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                                <input type="color" name="accent_color" id="inputAccentColor"
                                    class="form-control form-control-color border-0 p-0 rounded-circle shadow-sm"
                                    style="width: 40px; height: 40px;"
                                    value="<?= htmlspecialchars($branding['accent_color'] ?? '#ffc20e', ENT_QUOTES, 'UTF-8') ?>"
                                    title="Escolher cor de destaque">
                                <div>
                                    <div class="fw-bold text-dark small">Cor Secundária</div>
                                    <div class="text-muted x-small">Badges, links e detalhes.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logos Section -->
            <div class="nd-card mb-4 border-0 shadow-sm">
                <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-images text-info"></i>
                    <h5 class="nd-card-title mb-0">Logotipos</h5>
                </div>
                <div class="nd-card-body p-4">
                    <div class="mb-4">
                        <label class="nd-label">Logo do Módulo Administrativo</label>
                        <div class="nd-input-group">
                            <input type="text" name="admin_logo_url" class="nd-input bg-light border-0"
                                value="<?= htmlspecialchars($branding['admin_logo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="/assets/img/logo-admin.png" style="padding-left: 2.5rem;">
                            <i class="bi bi-image nd-input-icon text-muted"></i>
                        </div>
                         <div class="form-text small mt-1">Recomendado: Imagem PNG transparente, altura máx 50px.</div>
                    </div>

                    <div class="mb-0">
                        <label class="nd-label">Logo do Portal do Cliente</label>
                        <div class="nd-input-group">
                            <input type="text" name="portal_logo_url" class="nd-input bg-light border-0"
                                value="<?= htmlspecialchars($branding['portal_logo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="/assets/img/logo-portal.png" style="padding-left: 2.5rem;">
                            <i class="bi bi-laptop nd-input-icon text-muted"></i>
                        </div>
                         <div class="form-text small mt-1">Será exibido na tela de login e cabeçalho do portal.</div>
                    </div>
                </div>
                 <div class="nd-card-footer bg-light p-3 d-flex justify-content-end">
                    <button type="submit" class="nd-btn nd-btn-primary shadow-sm px-4">
                        <i class="bi bi-save me-1"></i> Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview Column -->
    <div class="col-12 col-lg-5">
        <div class="sticky-top" style="top: 2rem; z-index: 10;">
            <h6 class="text-muted text-uppercase fw-bold small mb-3 ls-1">Pré-visualização (Live Preview)</h6>
            
            <!-- Admin Preview -->
            <div class="nd-card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="nd-card-header bg-white border-bottom py-2">
                    <div class="small fw-bold text-muted"><i class="bi bi-layout-sidebar me-1"></i> Estilo do Painel Admin</div>
                </div>
                <div class="d-flex">
                    <!-- Fake Sidebar -->
                    <div id="previewSidebar" class="bg-dark p-3 d-flex flex-column align-items-center" style="width: 60px; min-height: 200px;">
                        <div class="rounded-circle bg-white bg-opacity-25 mb-4" style="width: 32px; height: 32px;"></div>
                        <div class="w-50 bg-white bg-opacity-10 rounded mb-2" style="height: 4px;"></div>
                        <div class="w-50 bg-white bg-opacity-10 rounded mb-2" style="height: 4px;"></div>
                         <div id="previewAccentBar" class="w-100 mt-2 rounded" style="height: 20px; background-color: var(--nd-gold-500);"></div>
                    </div>
                    <!-- Fake Content -->
                    <div class="flex-grow-1 bg-light p-3">
                         <div class="bg-white p-3 rounded shadow-sm border mb-3">
                              <h5 id="previewAppName" class="fw-bold mb-1" style="color: var(--nd-navy-900);">NimbusDocs</h5>
                              <p id="previewAppSubtitle" class="text-muted small mb-3">Portal seguro de documentos</p>
                              <button id="previewPrimaryBtn" class="btn btn-sm text-white px-3" style="background-color: var(--nd-navy-600);">Botão Principal</button>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Portal Preview -->
             <div class="nd-card border-0 shadow-sm overflow-hidden">
                <div class="nd-card-header bg-white border-bottom py-2">
                    <div class="small fw-bold text-muted"><i class="bi bi-window-desktop me-1"></i> Estilo do Portal</div>
                </div>
                <div class="p-0">
                     <!-- Fake Portal Header -->
                     <div id="previewPortalHeader" class="p-3 text-white d-flex align-items-center justify-content-between" style="background-color: var(--nd-navy-600);">
                         <span class="fw-bold"><i class="bi bi-box-seam me-2"></i> Logo Area</span>
                         <div class="d-flex gap-2">
                             <div class="bg-white bg-opacity-25 rounded-circle" style="width: 24px; height: 24px;"></div>
                         </div>
                     </div>
                     <div class="p-4 bg-white text-center">
                         <h5 class="fw-bold text-dark">Área do Cliente</h5>
                         <p class="text-muted small">Acesse seus documentos com segurança.</p>
                         <div class="mt-3">
                              <span id="previewAccentBadge" class="badge text-dark" style="background-color: var(--nd-gold-500);">Novo Documento</span>
                         </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const els = {
            appName: document.getElementById('inputAppName'),
            appSubtitle: document.getElementById('inputAppSubtitle'),
            primaryColor: document.getElementById('inputPrimaryColor'),
            accentColor: document.getElementById('inputAccentColor'),
            
            pAppName: document.getElementById('previewAppName'),
            pAppSubtitle: document.getElementById('previewAppSubtitle'),
            pBtn: document.getElementById('previewPrimaryBtn'),
            pHeader: document.getElementById('previewPortalHeader'),
            pAccentBar: document.getElementById('previewAccentBar'),
            pAccentBadge: document.getElementById('previewAccentBadge')
        };

        function updatePreview() {
            if(els.pAppName) els.pAppName.textContent = els.appName.value || 'Nome da Aplicação';
            if(els.pAppSubtitle) els.pAppSubtitle.textContent = els.appSubtitle.value || 'Descrição da aplicação';
            
            // Primary Color
            if(els.pBtn) els.pBtn.style.backgroundColor = els.primaryColor.value;
            if(els.pHeader) els.pHeader.style.backgroundColor = els.primaryColor.value;
            
            // Accent Color
            if(els.pAccentBar) els.pAccentBar.style.backgroundColor = els.accentColor.value;
            if(els.pAccentBadge) els.pAccentBadge.style.backgroundColor = els.accentColor.value;
        }

        // Listeners
        [els.appName, els.appSubtitle, els.primaryColor, els.accentColor].forEach(input => {
            if(input) input.addEventListener('input', updatePreview);
        });
        
        // Init
        updatePreview();
    });
</script>