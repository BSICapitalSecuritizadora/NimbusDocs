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

$appName = $branding['app_name'] ?? 'NimbusDocs';
$primaryColor = $branding['primary_color'] ?? '#00205b';
?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-shield-lock text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Autenticação em Dois Fatores (2FA)</h5>
                            <small class="text-muted">Adicione uma camada extra de segurança à sua conta</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($isEnabled): ?>
                        <!-- 2FA is enabled -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success bg-opacity-10 p-4 d-inline-flex mb-3">
                                <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-success">2FA Ativado</h5>
                            <p class="text-muted">Sua conta está protegida com autenticação em dois fatores.</p>
                        </div>

                        <hr>

                        <h6 class="mb-3">Desativar 2FA</h6>
                        <p class="text-muted small">Para desativar, digite o código atual do seu aplicativo autenticador.</p>

                        <form method="POST" action="/admin/2fa/disable">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            
                            <div class="mb-3">
                                <label for="code" class="form-label">Código de Verificação</label>
                                <input type="text" class="form-control form-control-lg text-center" 
                                       id="code" name="code" 
                                       pattern="[0-9]{6}" maxlength="6" 
                                       placeholder="000000" required 
                                       autocomplete="one-time-code" inputmode="numeric">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-shield-x me-2"></i>Desativar 2FA
                                </button>
                            </div>
                        </form>

                    <?php else: ?>
                        <!-- 2FA is not enabled -->
                        <div class="row">
                            <div class="col-md-6 text-center mb-4 mb-md-0">
                                <h6 class="mb-3">1. Escaneie o QR Code</h6>
                                <p class="text-muted small">Use o Google Authenticator, Microsoft Authenticator ou app similar.</p>
                                <div class="bg-white p-3 d-inline-block rounded border">
                                    <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">Ou digite manualmente:</small>
                                    <div class="bg-light rounded p-2 mt-1">
                                        <code class="text-dark user-select-all" style="font-size: 0.9rem; word-break: break-all;">
                                            <?= htmlspecialchars($secret) ?>
                                        </code>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-3">2. Digite o Código</h6>
                                <p class="text-muted small">Após escanear, digite o código de 6 dígitos exibido no app.</p>

                                <form method="POST" action="/admin/2fa/enable">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Código de Verificação</label>
                                        <input type="text" class="form-control form-control-lg text-center" 
                                               id="code" name="code" 
                                               pattern="[0-9]{6}" maxlength="6" 
                                               placeholder="000000" required 
                                               autocomplete="one-time-code" inputmode="numeric">
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-shield-check me-2"></i>Ativar 2FA
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Por que usar 2FA?</h6>
                            <ul class="mb-0 small">
                                <li>Protege sua conta mesmo se alguém descobrir sua senha</li>
                                <li>Bloqueia tentativas de acesso não autorizado</li>
                                <li>Atende requisitos de compliance de segurança</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3">
                <a href="/admin/settings" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Voltar para Configurações
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-format code input
document.getElementById('code')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
