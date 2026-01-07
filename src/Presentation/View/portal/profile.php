<?php
/**
 * @var array $user
 * @var string $csrfToken
 * @var array $flash
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="d-flex align-items-center mb-4">
            <h1 class="h3 fw-bold text-dark mb-0">Meu Perfil</h1>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><?= htmlspecialchars($flash['success']) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                <div><?= htmlspecialchars($flash['error']) ?></div>
            </div>
        <?php endif; ?>

        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center">
                <div class="user-avatar text-bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Informações Pessoais</h5>
                    <p class="mb-0 small text-secondary">Gerencie suas informações de cadastro</p>
                </div>
            </div>
            
            <div class="nd-card-body">
                <form action="/portal/profile" method="POST">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="full_name" class="nd-label">Nome Completo</label>
                            <input type="text" class="nd-input" id="full_name" name="full_name" 
                                   value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="document_number" class="nd-label">Documento (CPF/CNPJ)</label>
                            <input type="text" class="nd-input bg-light" id="document_number" 
                                   value="<?= htmlspecialchars($user['document_number'] ?? '') ?>" readonly disabled>
                            <div class="form-text small">O documento não pode ser alterado.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="nd-label">E-mail</label>
                            <input type="email" class="nd-input bg-light" id="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled>
                             <div class="form-text small">Para alterar o e-mail, entre em contato com o suporte.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone_number" class="nd-label">Telefone</label>
                            <input type="text" class="nd-input" id="phone_number" name="phone_number" 
                                   value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" placeholder="(00) 00000-0000">
                        </div>

                        <div class="col-12 text-end pt-2 border-top border-light-subtle mt-2">
                            <button type="submit" class="nd-btn nd-btn-primary px-4">
                                <i class="bi bi-check-lg me-2"></i> Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Simple mask for phone
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }
</script>
