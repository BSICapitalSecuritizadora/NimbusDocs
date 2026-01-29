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
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4 rounded-3" role="alert">
                <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                </div>
                <div class="fw-medium text-success-emphasis"><?= htmlspecialchars($flash['success']) ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4 rounded-3" role="alert">
                <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                </div>
                <div class="fw-medium text-danger-emphasis"><?= htmlspecialchars($flash['error']) ?></div>
                 <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="nd-card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <!-- Decorative Banner -->
            <div class="w-100" style="height: 140px; background-color: #dbeafe;"></div>
            
            <div class="nd-card-body position-relative px-5 pb-5">
                <!-- Avatar Section (Overlapping) -->
                <div class="position-absolute top-0 start-0 translate-middle-y ms-5">
                     <div class="user-avatar bg-white p-1 rounded-circle d-inline-block position-relative shadow-sm" style="margin-top: -10px;">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold display-5 shadow-sm" 
                             style="width: 100px; height: 100px; background: linear-gradient(135deg, #0d6efd, #0a58ca);">
                            <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                        </div>
                        <!-- Gold Dot -->
                        <div class="position-absolute bottom-0 end-0 bg-warning border border-4 border-white rounded-circle" 
                             style="width: 24px; height: 24px; bottom: 5px !important; right: 5px !important;"></div>
                     </div>
                </div>

                <!-- Header Actions -->
                <div class="d-flex justify-content-end pt-3 mb-4">
                     <span class="badge bg-light text-secondary border fw-medium px-3 py-2 rounded-pill d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success"></i> Conta Segura
                     </span>
                </div>

                <div class="mt-5">
                    <h5 class="fw-bold text-dark mb-1 h5">Meus Dados</h5>
                    <p class="text-secondary small mb-4">Mantenha seus dados atualizados para facilitar nossa comunicação.</p>

                    <form action="/portal/profile" method="POST">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                        
                        <div class="row g-4">
                            <!-- Nome -->
                            <div class="col-md-6">
                                <label for="full_name" class="form-label x-small fw-bold text-uppercase text-secondary ls-1 mb-2">Nome Completo</label>
                                <div class="nd-input-group position-relative">
                                    <i class="bi bi-person position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" class="form-control bg-light border-0 py-3 ps-5 rounded-3 fw-medium text-dark" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <!-- Documento -->
                            <div class="col-md-6">
                                <label for="document_number" class="form-label x-small fw-bold text-uppercase text-secondary ls-1 mb-2">Documento (CPF)</label>
                                <?php
                                    $doc = preg_replace('/\D/', '', $user['document_number'] ?? '');
                                    $maskedDoc = $doc;
                                    if (strlen($doc) === 11) {
                                        $maskedDoc = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
                                    } elseif (strlen($doc) === 14) {
                                        $maskedDoc = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
                                    }
                                ?>
                                <div class="nd-input-group position-relative">
                                    <i class="bi bi-card-heading position-absolute top-50 start-0 translate-middle-y ms-3 text-muted opacity-50"></i>
                                    <input type="text" class="form-control bg-light border-0 py-3 ps-5 rounded-3 text-muted" id="document_number" 
                                           value="<?= htmlspecialchars($maskedDoc) ?>" readonly disabled 
                                           style="cursor: not-allowed !important;">
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" data-bs-toggle="tooltip" title="Para alterar o documento, contate o suporte.">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label x-small fw-bold text-uppercase text-secondary ls-1 mb-2">E-mail Corporativo</label>
                                <div class="nd-input-group position-relative">
                                    <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-muted opacity-50"></i>
                                    <input type="email" class="form-control bg-light border-0 py-3 ps-5 rounded-3 text-muted" id="email" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled
                                           style="cursor: not-allowed !important;">
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" data-bs-toggle="tooltip" title="Para alterar o e-mail, contate o suporte.">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-2 text-muted x-small">
                                    <i class="bi bi-info-circle"></i>
                                    <span>Precisa alterar o e-mail? Contate nosso suporte.</span>
                                </div>
                            </div>

                            <!-- Telefone -->
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label x-small fw-bold text-uppercase text-secondary ls-1 mb-2">Telefone</label>
                                <div class="nd-input-group position-relative">
                                    <i class="bi bi-telephone position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" class="form-control bg-light border-0 py-3 ps-5 rounded-3 fw-medium text-dark" id="phone_number" name="phone_number" 
                                           value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" placeholder="(00) 00000-0000">
                                </div>
                            </div>

                            <div class="col-12 text-end pt-3 border-top border-light-subtle mt-4">
                                <button type="submit" class="nd-btn nd-btn-navy shadow-sm px-4 py-2 rounded-3 hover-scale d-inline-flex align-items-center gap-2" 
                                        style="background-color: var(--nd-navy-900); color: white;">
                                    <i class="bi bi-check-lg"></i> Salvar Alterações
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
