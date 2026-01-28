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
            <p class="text-muted mb-0 small">Segurança adicional para proteger sua conta contra acessos não autorizados</p>
        </div>
    </div>
    <a href="/admin/settings" class="nd-btn nd-btn-outline nd-btn-sm hover-shadow-sm transition-fast">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar para Configurações
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0" role="alert">
        <div class="p-2 bg-danger text-white rounded-circle me-3">
             <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
            <div class="fw-bold">Atenção!</div>
            <div class="small"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0" role="alert">
        <div class="p-2 bg-success text-white rounded-circle me-3">
             <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div class="fw-bold">Sucesso!</div>
            <div class="small"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="nd-card border-0 shadow-sm overflow-hidden">
            <div class="nd-card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-warning"></i>
                <h5 class="nd-card-title mb-0">Gerenciar Autenticação</h5>
            </div>
            
            <div class="nd-card-body p-0">
                <?php if ($isEnabled): ?>
                    <!-- 2FA Enabled State -->
                    <div class="text-center py-5 px-4">
                        <div class="mb-4 position-relative d-inline-block">
                             <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-success opacity-10" style="width: 120px; height: 120px;"></div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm position-relative" style="width: 80px; height: 80px;">
                                <i class="bi bi-shield-check text-success display-4"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Sua conta está protegida!</h4>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                            A autenticação em dois fatores está ativa. Qualquer novo acesso exigirá a validação pelo seu dispositivo móvel.
                        </p>

                        <div class="p-4 bg-light rounded-3 text-start mx-auto border" style="max-width: 500px;">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-exclamation-circle-fill text-danger mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Deseja desativar a segurança?</h6>
                                    <p class="small text-muted mb-0">Isso deixará sua conta vulnerável apenas com a senha. Confirme com seu token atual para prosseguir.</p>
                                </div>
                            </div>

                            <form method="POST" action="/admin/2fa/disable">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                
                                <div class="mb-3">
                                    <input type="text" class="nd-input text-center fs-5 py-3 fw-bold bg-white" 
                                           id="code" name="code" 
                                           pattern="[0-9]{6}" maxlength="6" 
                                           placeholder="Token atual (ex: 123456)" required 
                                           autocomplete="one-time-code" inputmode="numeric"
                                           style="letter-spacing: 0.2rem;">
                                </div>

                                <button type="submit" class="nd-btn nd-btn-danger w-100 shadow-sm">
                                    <i class="bi bi-shield-x me-2"></i> Confirmar Desativação
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- 2FA Disabled State (Setup) -->
                    <div class="row g-0">
                        <!-- Left: QR Code -->
                        <div class="col-md-6 border-end bg-light-subtle">
                            <div class="p-4 h-100 d-flex flex-column align-items-center text-center">
                                <div class="mb-3">
                                    <div class="badge bg-primary-subtle text-primary mb-2 px-3 py-2 rounded-pill">Passo 1</div>
                                    <h6 class="fw-bold text-dark">Sincronizar Dispositivo</h6>
                                    <p class="small text-muted mb-0 mx-auto" style="max-width: 250px;">
                                        Abra seu app autenticador (Google ou Microsoft) e escaneie o código.
                                    </p>
                                </div>
                                
                                <div class="bg-white p-3 border rounded shadow-sm mb-3 position-relative group-hover">
                                    <img src="<?= htmlspecialchars($qrCodeUrl, ENT_QUOTES, 'UTF-8') ?>" alt="QR Code" class="img-fluid" style="max-width: 160px;">
                                     <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-90 opacity-0 transition-fast hover-opacity-100">
                                        <span class="small fw-bold text-dark"><i class="bi bi-camera-fill me-1"></i> Escaneie-me</span>
                                    </div>
                                </div>
                                
                                <div class="w-100 px-3">
                                    <p class="x-small text-muted text-uppercase fw-bold mb-2 ls-1">Não consegue ler o QR Code?</p>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-white font-monospace text-center" value="<?= htmlspecialchars($secret, ENT_QUOTES, 'UTF-8') ?>" readonly id="secretKey">
                                        <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                    <div class="form-text x-small mt-1">Insira esta chave manualmente no app.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Validation -->
                        <div class="col-md-6 bg-white">
                            <div class="p-4 h-100 d-flex flex-column justify-content-center">
                                 <div class="mb-4">
                                    <div class="badge bg-primary-subtle text-primary mb-2 px-3 py-2 rounded-pill">Passo 2</div>
                                    <h6 class="fw-bold text-dark">Validar Vínculo</h6>
                                    <p class="small text-muted">Digite o código de 6 dígitos que apareceu no seu celular para confirmar.</p>
                                </div>

                                <form method="POST" action="/admin/2fa/enable">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    
                                    <div class="mb-4">
                                        <label for="code" class="nd-label text-muted text-uppercase x-small fw-bold">Token de Validação</label>
                                        <div class="position-relative">
                                            <input type="text" class="nd-input text-center fs-3 py-3 fw-bold" 
                                                id="code" name="code" 
                                                pattern="[0-9]{6}" maxlength="6" 
                                                placeholder="000 000" required 
                                                autocomplete="one-time-code" inputmode="numeric"
                                                style="letter-spacing: 0.3rem; height: 60px;">
                                            <div class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted opacity-25">
                                                <i class="bi bi-phone fs-4"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="nd-btn nd-btn-primary w-100 shadow-sm py-2 fs-6">
                                        Ativar Proteção <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
             <div class="nd-card-footer bg-light py-3 px-4 border-top">
                <div class="row text-center text-muted small">
                    <div class="col-4 border-end">
                         <div class="fw-bold text-dark"><i class="bi bi-check-circle-fill text-success me-1"></i> Seguro</div>
                         <div class="x-small">Criptografia Ponta-a-Ponta</div>
                    </div>
                    <div class="col-4 border-end">
                         <div class="fw-bold text-dark"><i class="bi bi-phone-fill text-primary me-1"></i> Apps</div>
                         <div class="x-small">Google ou Microsoft</div>
                    </div>
                     <div class="col-4">
                         <div class="fw-bold text-dark"><i class="bi bi-clock-history text-warning me-1"></i> Rápido</div>
                         <div class="x-small">Tokens de 30s</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code')?.addEventListener('input', function(e) {
    // Remove não numéricos
    let val = this.value.replace(/[^0-9]/g, '');
    this.value = val.slice(0, 6);
});

function copySecret() {
    const copyText = document.getElementById("secretKey");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // Mobile
    navigator.clipboard.writeText(copyText.value).then(() => {
        // Feedback visual simples
        const btn = document.querySelector('button[onclick="copySecret()"]');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check text-success"></i>';
        setTimeout(() => btn.innerHTML = originalHtml, 1500);
    });
}
</script>
