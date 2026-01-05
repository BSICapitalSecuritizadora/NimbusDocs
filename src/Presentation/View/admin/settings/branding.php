<?php

/** @var array $branding */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */
?>
<h1 class="h4 mb-3">Branding e identidade visual</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success py-2 small">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="/admin/settings/branding/save">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <h2 class="h6 mb-3">Identidade</h2>

            <div class="mb-3">
                <label class="form-label">Nome da aplicação</label>
                <input type="text"
                    name="app_name"
                    class="form-control form-control-sm"
                    value="<?= htmlspecialchars($branding['app_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text">Ex.: NimbusDocs, NimbusDocs BSI, etc.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Subtítulo / descrição curta</label>
                <input type="text"
                    name="app_subtitle"
                    class="form-control form-control-sm"
                    value="<?= htmlspecialchars($branding['app_subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <hr>

            <h2 class="h6 mb-3">Cores</h2>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Cor primária</label>
                    <input type="color"
                        name="primary_color"
                        class="form-control form-control-color form-control-sm"
                        value="<?= htmlspecialchars($branding['primary_color'] ?? '#00205b', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="form-text small">Usada em navbars e botões principais.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cor de destaque</label>
                    <input type="color"
                        name="accent_color"
                        class="form-control form-control-color form-control-sm"
                        value="<?= htmlspecialchars($branding['accent_color'] ?? '#ffc20e', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="form-text small">Usada em badges, detalhes e destaques.</div>
                </div>
            </div>

            <hr>

            <h2 class="h6 mb-3">Logos (URLs)</h2>

            <div class="mb-3">
                <label class="form-label">Logo do módulo administrativo</label>
                <input type="text"
                    name="admin_logo_url"
                    class="form-control form-control-sm"
                    value="<?= htmlspecialchars($branding['admin_logo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text small">
                    URL absoluta ou caminho relativo. Ex.: <code>/assets/img/logo-admin.png</code>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Logo do portal do usuário</label>
                <input type="text"
                    name="portal_logo_url"
                    class="form-control form-control-sm"
                    value="<?= htmlspecialchars($branding['portal_logo_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-text small">
                    Ex.: <code>/assets/img/logo-portal.png</code>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-sm">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>