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
    <div class="alert alert-success d-flex align-items-center mb-4 nd-alert" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div class="small fw-medium"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4 nd-alert" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div class="small fw-medium"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="nd-card h-100">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-workspace text-primary"></i>
                <h5 class="nd-card-title mb-0">Portal do Usuário</h5>
            </div>
            <div class="nd-card-body">
                <form method="post" action="/admin/settings/notifications/save">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="d-flex flex-column gap-0">
                        <!-- Notify New Submission -->
                        <div class="p-3 bg-white rounded border border-light-subtle mb-3 d-flex align-items-center justify-content-between hover-shadow-sm transition-fast">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="bg-primary-subtle rounded-circle p-2 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-file-earmark-plus-fill"></i>
                                </div>
                                <div>
                                    <label class="fw-bold text-dark mb-1 d-block" for="portal_notify_new_submission">Nova Submissão</label>
                                    <div class="small text-muted" style="max-width: 400px;">Enviar e-mail ao usuário confirmando o recebimento de uma nova submissão.</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_new_submission"
                                    name="portal_notify_new_submission"
                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                    <?= $has('portal.notify.new_submission') ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Notify Status Change -->
                        <div class="p-3 bg-white rounded border border-light-subtle mb-3 d-flex align-items-center justify-content-between hover-shadow-sm transition-fast">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="bg-success-subtle rounded-circle p-2 text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div>
                                    <label class="fw-bold text-dark mb-1 d-block" for="portal_notify_status_change">Alteração de Status</label>
                                    <div class="small text-muted" style="max-width: 400px;">Notificar o usuário quando o status de sua submissão for alterado pelo analista.</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_status_change"
                                    name="portal_notify_status_change"
                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                    <?= $has('portal.notify.status_change') ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Notify Response Upload -->
                        <div class="p-3 bg-white rounded border border-light-subtle mb-3 d-flex align-items-center justify-content-between hover-shadow-sm transition-fast">
                             <div class="d-flex gap-3 align-items-center">
                                <div class="bg-info-subtle rounded-circle p-2 text-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                </div>
                                <div>
                                    <label class="fw-bold text-dark mb-1 d-block" for="portal_notify_response_upload">Documento de Resposta</label>
                                    <div class="small text-muted" style="max-width: 400px;">Avisar o usuário quando um administrador anexar um arquivo/resposta à submissão.</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_response_upload"
                                    name="portal_notify_response_upload"
                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                    <?= $has('portal.notify.response_upload') ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Notify Access Link -->
                         <div class="p-3 bg-white rounded border border-light-subtle mb-0 d-flex align-items-center justify-content-between hover-shadow-sm transition-fast">
                             <div class="d-flex gap-3 align-items-center">
                                <div class="bg-warning-subtle rounded-circle p-2 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-key-fill"></i>
                                </div>
                                <div>
                                    <label class="fw-bold text-dark mb-1 d-block" for="portal_notify_access_link">Link de Acesso (Magic Link)</label>
                                    <div class="small text-muted" style="max-width: 400px;">Enviar e-mail automático com o token de acesso sempre que solicitado.</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="portal_notify_access_link"
                                    name="portal_notify_access_link"
                                    style="width: 3em; height: 1.5em; cursor: pointer;"
                                    <?= $has('portal.notify.access_link') ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <div class="nd-card mb-4 bg-light text-dark border-0">
             <div class="nd-card-header bg-transparent border-bottom border-light-subtle d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-muted"></i>
                <h6 class="fw-bold mb-0 text-muted text-uppercase small">Infraestrutura</h6>
            </div>
            <div class="nd-card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <i class="bi bi-microsoft text-primary" style="font-size: 2rem;"></i>
                    <div>
                        <h6 class="fw-bold mb-1 small text-uppercase text-muted">Provedor de E-mail</h6>
                        <div class="fw-bold text-dark">Microsoft Graph API</div>
                        <div class="badge bg-success bg-opacity-10 text-success mt-1 border border-success border-opacity-25">
                            <i class="bi bi-check-circle me-1"></i>Conectado
                        </div>
                    </div>
                </div>
                <hr class="border-light-subtle">
                 <p class="text-muted small mb-0">
                    O sistema utiliza a API do Office 365 para garantir alta entregabilidade das mensagens transacionais.
                </p>
            </div>
        </div>

        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-activity text-danger"></i>
                <h5 class="nd-card-title mb-0">Fila e Logs</h5>
            </div>
            <div class="nd-card-body">
                <p class="text-muted small mb-3">
                    Monitore em tempo real o envio das mensagens. Caso algum e-mail falhe, você poderá reprocessá-lo na auditoria.
                </p>
                <div class="d-grid gap-2">
                    <a href="/admin/notifications/outbox" class="nd-btn nd-btn-outline">
                        <i class="bi bi-inbox me-2"></i> Ver Auditoria de Envios
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>