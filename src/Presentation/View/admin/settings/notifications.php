<?php

/** @var array $settings */
/** @var string $csrfToken */
/** @var ?string $success */

$has = fn(string $key, bool $default = true)
=> array_key_exists($key, $settings)
    ? ($settings[$key] === '1')
    : $default;
?>

<h1 class="h4 mb-3">Configurações de notificações</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-sm py-2">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="/admin/settings/notifications/save">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <h2 class="h6 mb-3">Portal do Usuário</h2>

            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" role="switch"
                    id="portal_notify_new_submission"
                    name="portal_notify_new_submission"
                    <?= $has('portal.notify.new_submission') ? 'checked' : '' ?>>
                <label class="form-check-label" for="portal_notify_new_submission">
                    Enviar e-mail ao usuário ao criar nova submissão
                </label>
            </div>

            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" role="switch"
                    id="portal_notify_status_change"
                    name="portal_notify_status_change"
                    <?= $has('portal.notify.status_change') ? 'checked' : '' ?>>
                <label class="form-check-label" for="portal_notify_status_change">
                    Enviar e-mail ao usuário quando o status da submissão for alterado
                </label>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" role="switch"
                    id="portal_notify_response_upload"
                    name="portal_notify_response_upload"
                    <?= $has('portal.notify.response_upload') ? 'checked' : '' ?>>
                <label class="form-check-label" for="portal_notify_response_upload">
                    Enviar e-mail ao usuário quando forem anexados documentos de resposta
                </label>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-sm">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>