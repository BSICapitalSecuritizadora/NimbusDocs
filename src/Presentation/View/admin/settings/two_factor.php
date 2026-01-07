<?php
/**
 * Two-Factor Authentication Setup View
 * @var string $csrfToken
 * @var string $secret
 * @var string $qrCodeUrl
 * @var bool $isEnabled
 * @var string|null $error
 * @var string|null $success
 */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600); color: #fff;">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Autenticação em Dois Fatores (2FA)</h1>
            <p class="text-muted mb-0 small">Adicione uma camada extra de segurança à sua conta</p>
        </div>
    </div>
    <a href="/admin/settings" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <div><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-check" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Gerenciar Autenticação</h5>
            </div>
            
            <div class="nd-card-body">
                <?php if ($isEnabled): ?>
                    <!-- 2FA Enabled State -->
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1);">
                                <i class="bi bi-shield-check text-success" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-success mb-2">Proteção Ativa</h4>
                        <p class="text-muted mb-4">Sua conta está protegida com autenticação em dois fatores.</p>

                        <div class="p-3 bg-light rounded text-start mx-auto" style="max-width: 500px;">
                            <h6 class="fw-bold text-dark mb-2">Manutenção de Segurança</h6>
                            <p class="small text-muted mb-3">Para desativar a proteção, confirme sua identidade com o token atual.</p>

                            <form method="POST" action="/admin/2fa/disable">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                
                                <div class="mb-3">
                                    <input type="text" class="nd-input text-center fs-5 py-2 fw-bold" 
                                           id="code" name="code" 
                                           pattern="[0-9]{6}" maxlength="6" 
                                           placeholder="000 000" required 
                                           autocomplete="one-time-code" inputmode="numeric"
                                           style="letter-spacing: 0.2em;">
                                </div>

                                <button type="submit" class="nd-btn nd-btn-danger w-100">
                                    <i class="bi bi-shield-x me-2"></i>Desativar Proteção
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- 2FA Disabled State (Setup) -->
                    <div class="row g-4">
                        <div class="col-md-6 text-center border-end">
                            <div class="pe-md-3">
                                <h6 class="fw-bold text-dark mb-3">Sincronização de Dispositivo</h6>
                                <p class="small text-muted mb-4">Utilize seu aplicativo autenticador corporativo (Microsoft Authenticator ou Google Authenticator) para ler o código abaixo.</p>
                                
                                <div class="p-3 border rounded d-inline-block bg-white mb-3">
                                    <img src="<?= htmlspecialchars($qrCodeUrl, ENT_QUOTES, 'UTF-8') ?>" alt="QR Code" class="img-fluid" style="max-width: 180px;">
                                </div>
                                
                                <div>
                                    <p class="small text-muted mb-1">Chave de Configuração Manual</p>
                                    <div class="p-2 bg-light rounded border d-inline-block">
                                        <code class="text-dark fw-bold user-select-all"><?= htmlspecialchars($secret, ENT_QUOTES, 'UTF-8') ?></code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="ps-md-3">
                                <h6 class="fw-bold text-dark mb-3">Validação de Segurança</h6>
                                <p class="small text-muted mb-4">Insira o token de 6 dígitos gerado pelo seu dispositivo para confirmar a vinculação.</p>

                                <form method="POST" action="/admin/2fa/enable">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    
                                    <div class="mb-4">
                                        <label for="code" class="nd-label">Token de Validação</label>
                                        <input type="text" class="nd-input text-center fs-4 py-2 fw-bold" 
                                               id="code" name="code" 
                                               pattern="[0-9]{6}" maxlength="6" 
                                               placeholder="000 000" required 
                                               autocomplete="one-time-code" inputmode="numeric"
                                               style="letter-spacing: 0.2em;">
                                    </div>

                                    <button type="submit" class="nd-btn nd-btn-primary w-100">
                                        <i class="bi bi-shield-lock me-2"></i>Ativar Proteção
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <div class="d-inline-flex gap-4 text-muted small">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-success"></i> Protocolo de Segurança Ativo
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bank text-success"></i> Conformidade Financeira
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle text-success"></i> Configuração Simplificada
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>
