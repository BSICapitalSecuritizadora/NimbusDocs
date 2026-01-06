<?php

/** @var array $branding */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-palette-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Branding e Identidade Visual</h1>
            <p class="text-muted mb-0 small">Personalize as cores, logotipos e textos da aplicação.</p>
        </div>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="nd-alert nd-alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-brush" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Personalização</h5>
    </div>
    <div class="nd-card-body">
        <form method="post" action="/admin/settings/branding/save">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <h6 class="text-muted text-uppercase fw-bold small mb-3">Identidade</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="nd-label">Nome da aplicação</label>
                    <input type="text" name="app_name" class="nd-input"
                        value="<?= htmlspecialchars($branding['app_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="form-text small mt-1"><i class="bi bi-info-circle me-1"></i> Ex.: NimbusDocs, NimbusDocs BSI, etc.</div>
                </div>
                <div class="col-md-6">
                    <label class="nd-label">Subtítulo / descrição curta</label>
                    <input type="text" name="app_subtitle" class="nd-input"
                        value="<?= htmlspecialchars($branding['app_subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <hr class="my-4" style="border-color: var(--nd-gray-200);">

            <h6 class="text-muted text-uppercase fw-bold small mb-3">Cores</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="nd-label">Cor primária</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" name="primary_color" class="form-control form-control-color"
                            value="<?= htmlspecialchars($branding['primary_color'] ?? '#00205b', ENT_QUOTES, 'UTF-8') ?>"
                            title="Escolher cor">
                        <small class="text-muted">Navbars e botões</small>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="nd-label">Cor de destaque</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" name="accent_color" class="form-control form-control-color"
                            value="<?= htmlspecialchars($branding['accent_color'] ?? '#ffc20e', ENT_QUOTES, 'UTF-8') ?>"
                            title="Escolher cor">
                        <small class="text-muted">Badges e detalhes</small>
                    </div>
                </div>
            </div>

            <hr class="my-4" style="border-color: var(--nd-gray-200);">

            <h6 class="text-muted text-uppercase fw-bold small mb-3">Logotipos (URLs)</h6>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="nd-label">Logo do módulo administrativo</label>
                    <div class="nd-input-group">
                        <span class="nd-input-group-text"><i class="bi bi-image"></i></span>
                        <input type="text" name="admin_logo_url" class="nd-input"
                            value="<?= htmlspecialchars($branding['admin_logo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="/assets/img/logo-admin.png">
                    </div>
                    <div class="form-text small mt-1">URL absoluta ou caminho relativo para a imagem da logo na área admin.</div>
                </div>

                <div class="col-12">
                    <label class="nd-label">Logo do portal do usuário</label>
                    <div class="nd-input-group">
                        <span class="nd-input-group-text"><i class="bi bi-image"></i></span>
                        <input type="text" name="portal_logo_url" class="nd-input"
                            value="<?= htmlspecialchars($branding['portal_logo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="/assets/img/logo-portal.png">
                    </div>
                    <div class="form-text small mt-1">Caminho para a logo exibida no portal do cliente.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end pt-2">
                <button type="submit" class="nd-btn nd-btn-primary">
                    <i class="bi bi-save me-1"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>