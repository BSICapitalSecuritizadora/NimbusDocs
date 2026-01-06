<?php
/**
 * Two-Factor Authentication Setup View
 * @var string $csrfToken
 * @var string $secret
 * @var string $qrCodeUrl
 * @var bool $isEnabled
 * @var string|null $error
 * @var string|null $success
 * @var array $branding
 */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-lock-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Autenticação em Dois Fatores (2FA)</h1>
            <p class="text-muted mb-0 small">Adicione uma camada extra de segurança à sua conta.</p>
        </div>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="nd-alert nd-alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($error) ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success) ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="nd-card">
            <?php if ($isEnabled): ?>
                <!-- 2FA is enabled -->
                <div class="nd-card-body text-center py-5">
                    <div class="nd-avatar nd-avatar-xl bg-success bg-opacity-10 text-success mx-auto mb-4">
                        <i class="bi bi-shield-check" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-2">2FA Ativado</h5>
                    <p class="text-muted mb-5">Sua conta está protegida com autenticação em dois fatores.</p>

                    <div class="nd-card bg-light border text-start mx-auto" style="max-width: 500px;">
                        <div class="nd-card-body">
                            <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-shield-x me-2"></i>Desativar 2FA</h6>
                            <p class="text-muted small mb-3">Para desativar, digite o código atual do seu aplicativo autenticador.</p>

                            <form method="POST" action="/admin/2fa/disable">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                
                                <div class="mb-3">
                                    <label for="code_disable" class="nd-label">Código de Verificação</label>
                                    <input type="text" class="nd-input text-center fw-bold fs-5" 
                                           id="code_disable" name="code" 
                                           pattern="[0-9]{6}" maxlength="6" 
                                           placeholder="000000" required 
                                           autocomplete="one-time-code" inputmode="numeric"
                                           style="letter-spacing: 0.2rem;">
                                </div>

                                <button type="submit" class="nd-btn nd-btn-danger w-100">
                                    Desativar 2FA
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- 2FA is not enabled -->
                <div class="nd-card-header">
                    <h5 class="nd-card-title mb-0">Configurar 2FA</h5>
                </div>
                <div class="nd-card-body">
                    <div class="row g-4">
                        <div class="col-md-6 text-center border-end-md">
                            <h6 class="fw-bold mb-3 text-primary">1. Escaneie o QR Code</h6>
                            <p class="text-muted small mb-3">Abra seu app autenticador (Google Authenticator, Authy, etc) e escaneie a imagem abaixo.</p>
                            
                            <div class="bg-white p-3 d-inline-block rounded border mb-3">
                                <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code" class="img-fluid" style="max-width: 180px;">
                            </div>
                            
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">Ou digite manualmente:</small>
                                <div class="bg-light rounded p-2 d-inline-block border">
                                    <code class="text-dark user-select-all fw-bold" style="font-size: 0.9rem;">
                                        <?= htmlspecialchars($secret) ?>
                                    </code>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <div class="px-md-3">
                                <h6 class="fw-bold mb-3 text-primary">2. Confirme o Código</h6>
                                <p class="text-muted small mb-4">Após escanear, digite o código de 6 dígitos gerado pelo seu aplicativo para confirmar a ativação.</p>

                                <form method="POST" action="/admin/2fa/enable">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    
                                    <div class="mb-4">
                                        <label for="code_enable" class="nd-label">Código de 6 dígitos</label>
                                        <div class="position-relative">
                                            <div class="position-absolute top-50 start-0 translate-middle-y ps-3 pointer-events-none">
                                                <i class="bi bi-key text-muted fs-5"></i>
                                            </div>
                                            <input type="text" class="nd-input text-center fw-bold fs-5" 
                                                   id="code_enable" name="code" 
                                                   pattern="[0-9]{6}" maxlength="6" 
                                                   placeholder="000000" required 
                                                   autocomplete="one-time-code" inputmode="numeric"
                                                   style="letter-spacing: 0.2rem;">
                                        </div>
                                    </div>

                                    <button type="submit" class="nd-btn nd-btn-primary w-100">
                                        <i class="bi bi-check-lg me-2"></i>Ativar Autenticação (2FA)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <div class="nd-alert nd-alert-info mb-0">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div>
                                <h6 class="alert-heading fw-bold small mb-1">Por que usar 2FA?</h6>
                                <ul class="mb-0 small ps-3">
                                    <li>Protege sua conta mesmo se a senha for comprometida.</li>
                                    <li>Adiciona uma barreira extra contra acessos não autorizados.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-format code input
const setupInputs = ['code_enable', 'code_disable'];
setupInputs.forEach(id => {
    document.getElementById(id)?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
    });
});
</script>
