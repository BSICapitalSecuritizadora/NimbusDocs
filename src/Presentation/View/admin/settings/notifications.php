<?php

/** @var array $settings */
/** @var string $csrfToken */
/** @var ?string $success */

$has = fn(string $key, bool $default = true)
=> array_key_exists($key, $settings)
    ? ($settings[$key] === '1')
    : $default;
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-bell-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Configurações de Notificações</h1>
            <p class="text-muted mb-0 small">Gerencie quando e como os usuários são notificados.</p>
        </div>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-envelope-paper" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Preferências de E-mail</h5>
    </div>
    <div class="nd-card-body">
        <form method="post" action="/admin/settings/notifications/save">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <h6 class="text-muted text-uppercase fw-bold small mb-3">Portal do Usuário</h6>
            
            <div class="d-flex flex-column gap-3">
                <div class="form-check form-switch p-0 d-flex align-items-center gap-3 ps-5 bg-light rounded p-3 border">
                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" style="width: 3em; height: 1.5em;"
                        id="portal_notify_new_submission"
                        name="portal_notify_new_submission"
                        <?= $has('portal.notify.new_submission') ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark fw-medium mb-0 flex-grow-1" for="portal_notify_new_submission">
                        Nova Submissão
                        <small class="d-block text-muted fw-normal mt-1">Enviar e-mail de confirmação ao usuário quando ele criar uma nova submissão.</small>
                    </label>
                </div>

                <div class="form-check form-switch p-0 d-flex align-items-center gap-3 ps-5 bg-light rounded p-3 border">
                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" style="width: 3em; height: 1.5em;"
                        id="portal_notify_status_change"
                        name="portal_notify_status_change"
                        <?= $has('portal.notify.status_change') ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark fw-medium mb-0 flex-grow-1" for="portal_notify_status_change">
                        Mudança de Status
                        <small class="d-block text-muted fw-normal mt-1">Notificar o usuário sempre que o status de sua submissão for alterado.</small>
                    </label>
                </div>

                <div class="form-check form-switch p-0 d-flex align-items-center gap-3 ps-5 bg-light rounded p-3 border">
                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" style="width: 3em; height: 1.5em;"
                        id="portal_notify_response_upload"
                        name="portal_notify_response_upload"
                        <?= $has('portal.notify.response_upload') ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark fw-medium mb-0 flex-grow-1" for="portal_notify_response_upload">
                        Documentos de Resposta
                        <small class="d-block text-muted fw-normal mt-1">Enviar e-mail quando novos documentos forem anexados à submissão.</small>
                    </label>
                </div>

                <div class="form-check form-switch p-0 d-flex align-items-center gap-3 ps-5 bg-light rounded p-3 border">
                    <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" style="width: 3em; height: 1.5em;"
                        id="portal_notify_access_link"
                        name="portal_notify_access_link"
                        <?= $has('portal.notify.access_link') ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark fw-medium mb-0 flex-grow-1" for="portal_notify_access_link">
                        Link de Acesso
                        <small class="d-block text-muted fw-normal mt-1">Enviar e-mail automático ao gerar um link de acesso único (magic link).</small>
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="nd-btn nd-btn-primary">
                    <i class="bi bi-save me-1"></i> Salvar Preferências
                </button>
            </div>
        </form>
    </div>
</div>