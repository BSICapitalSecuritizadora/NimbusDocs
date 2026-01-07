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
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
            <i class="bi bi-bell-fill"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Configurações de Notificações</h1>
            <p class="text-muted mb-0 small">Gerencie quando e como os usuários são notificados</p>
        </div>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <div><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-envelope text-muted"></i>
                <h5 class="nd-card-title mb-0">Portal do Usuário</h5>
            </div>
            <div class="nd-card-body">
                <form method="post" action="/admin/settings/notifications/save">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="d-flex flex-column gap-3">
                        <!-- Notify New Submission -->
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div>
                                <label class="fw-bold text-dark mb-1" for="portal_notify_new_submission">Nova Submissão</label>
                                <div class="small text-muted">Enviar e-mail ao usuário quando ele criar uma nova submissão.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_new_submission"
                                    name="portal_notify_new_submission"
                                    style="width: 3em; height: 1.5em;"
                                    <?= $has('portal.notify.new_submission') ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Notify Status Change -->
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div>
                                <label class="fw-bold text-dark mb-1" for="portal_notify_status_change">Alteração de Status</label>
                                <div class="small text-muted">Enviar e-mail quando o status de um documento mudar (ex: Aprovado).</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_status_change"
                                    name="portal_notify_status_change"
                                    style="width: 3em; height: 1.5em;"
                                    <?= $has('portal.notify.status_change') ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Notify Response Upload -->
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div>
                                <label class="fw-bold text-dark mb-1" for="portal_notify_response_upload">Documento de Resposta</label>
                                <div class="small text-muted">Enviar e-mail quando um administrador anexar um arquivo de resposta.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_response_upload"
                                    name="portal_notify_response_upload"
                                    style="width: 3em; height: 1.5em;"
                                    <?= $has('portal.notify.response_upload') ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Notify Access Link -->
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div>
                                <label class="fw-bold text-dark mb-1" for="portal_notify_access_link">Link de Acesso</label>
                                <div class="small text-muted">Enviar e-mail automaticamente ao gerar um Magic Link de acesso.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_access_link"
                                    name="portal_notify_access_link"
                                    style="width: 3em; height: 1.5em;"
                                    <?= $has('portal.notify.access_link') ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="nd-card bg-light border-0">
            <div class="nd-card-body">
                <h6 class="fw-bold text-dark mb-3">Sobre as Notificações</h6>
                <p class="text-muted small">
                    O sistema utiliza o Microsoft Graph API para envio de e-mails.
                </p>
                <hr>
                <h6 class="fw-bold text-dark mb-3">Logs de Envio</h6>
                <p class="text-muted small mb-3">
                    Pode ser monitorado o status de entrega dos e-mails através dos logs do sistema ou da caixa de saída.
                </p>
                <a href="/admin/monitoring" class="nd-btn nd-btn-outline nd-btn-sm w-100">
                    <i class="bi bi-activity me-1"></i> Ver Monitoramento
                </a>
            </div>
        </div>
    </div>
</div>