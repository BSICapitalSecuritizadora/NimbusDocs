<?php
/**
 * @var array $user
 * @var string $csrfToken
 * @var array $flash
 */
?>

<!-- Header -->
<div class="row justify-content-center mb-5">
    <div class="col-lg-8">
        <h1 class="h3 fw-bold text-dark mb-1">Meu Perfil</h1>
        <p class="text-secondary mb-0">Gerencie suas informações pessoais e de contato.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4 fade show" role="alert">
                <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                <div><?= htmlspecialchars($flash['success']) ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4 fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
                <div><?= htmlspecialchars($flash['error']) ?></div>
                 <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="nd-card border-0 shadow-sm overflow-hidden">
            <!-- decorative banner -->
            <div class="bg-primary-subtle w-100" style="height: 120px;"></div>
            
            <div class="nd-card-body position-relative pt-0 px-4 pb-4">
                <!-- Avatar -->
                <div class="position-absolute top-0 start-0 translate-middle-y ms-4">
                     <div class="user-avatar bg-white p-1 rounded-circle shadow-sm d-inline-block">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold display-6" 
                             style="width: 90px; height: 90px;">
                            <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                        </div>
                     </div>
                </div>

                <div class="d-flex justify-content-end pt-3 mb-4">
                     <span class="badge bg-light text-secondary border fw-normal px-3 py-2">
                        <i class="bi bi-shield-lock me-1"></i> Conta Segura
                     </span>
                </div>

                <div class="mt-4 pt-2">
                    <h5 class="fw-bold text-dark mb-1">Meus Dados</h5>
                    <p class="text-secondary small mb-4">Mantenha seus dados atualizados para facilitar nossa comunicação.</p>

                    <form action="/portal/profile" method="POST">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                        
                        <div class="row g-4">
                            <!-- Nome -->
                            <div class="col-md-6">
                                <label for="full_name" class="form-label small fw-bold text-uppercase text-muted ls-1">Nome Completo</label>
                                <div class="nd-input-group">
                                    <input type="text" class="nd-input ps-5" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                                    <i class="bi bi-person nd-input-icon text-muted"></i>
                                </div>
                            </div>
                            
                            <!-- Documento -->
                            <div class="col-md-6">
                                <label for="document_number" class="form-label small fw-bold text-uppercase text-muted ls-1">Documento (CPF)</label>
                                <?php
                                    $doc = preg_replace('/\D/', '', $user['document_number'] ?? '');
                                    $maskedDoc = $doc;
                                    if (strlen($doc) === 11) {
                                        $maskedDoc = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
                                    } elseif (strlen($doc) === 14) {
                                        $maskedDoc = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
                                    }
                                ?>
                                <div class="nd-input-group">
                                    <input type="text" class="nd-input bg-light ps-5 cursor-not-allowed" id="document_number" 
                                           value="<?= htmlspecialchars($maskedDoc) ?>" readonly disabled 
                                           title="Este campo não pode ser alterado">
                                    <i class="bi bi-card-heading nd-input-icon text-muted opacity-50"></i>
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" data-bs-toggle="tooltip" title="Para alterar o documento, contate o suporte.">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-uppercase text-muted ls-1">E-mail Corporativo</label>
                                <div class="nd-input-group">
                                    <input type="email" class="nd-input bg-light ps-5 cursor-not-allowed" id="email" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled>
                                    <i class="bi bi-envelope nd-input-icon text-muted opacity-50"></i>
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" data-bs-toggle="tooltip" title="Para alterar o e-mail, contate o suporte.">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                </div>
                                <div class="form-text x-small text-muted mt-1"><i class="bi bi-info-circle me-1"></i> Precisa alterar o e-mail? Contate nosso suporte.</div>
                            </div>

                            <!-- Telefone -->
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label small fw-bold text-uppercase text-muted ls-1">Telefone</label>
                                <div class="nd-input-group">
                                    <input type="text" class="nd-input ps-5" id="phone_number" name="phone_number" 
                                           value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" placeholder="(00) 00000-0000">
                                    <i class="bi bi-telephone nd-input-icon text-muted"></i>
                                </div>
                            </div>

                            <div class="col-12 text-end pt-3 border-top border-light-subtle mt-2">
                                <button type="submit" class="nd-btn nd-btn-primary px-4 py-2 hover-lift shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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
    
    // Init tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
