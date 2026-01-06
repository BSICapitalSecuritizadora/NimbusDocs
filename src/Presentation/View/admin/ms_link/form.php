<?php
/** @var string $csrfToken */
/** @var string|null $error */
/** @var string|null $success */
/** @var array $old */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-microsoft text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Vincular Conta Microsoft</h1>
            <p class="text-muted mb-0 small">Conecte sua conta administrativa com o Azure AD</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="nd-card">
      <div class="nd-card-header d-flex align-items-center gap-2">
         <i class="bi bi-link-45deg" style="color: var(--nd-gold-500);"></i>
         <h5 class="nd-card-title mb-0">Dados de Vinculação</h5>
      </div>
      <div class="nd-card-body">
        
        <?php if (!empty($error)): ?>
          <div class="nd-alert nd-alert-danger mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
          <div class="nd-alert nd-alert-success mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        <?php endif; ?>

        <form method="post" action="/admin/ms-link">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-4">
            <label class="nd-label">E-mail do Admin</label>
            <div class="nd-input-group">
                <input type="email" name="email" class="nd-input" 
                    value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="admin@exemplo.com"
                    required style="padding-left: 2.5rem;">
                <i class="bi bi-envelope nd-input-icon"></i>
            </div>
          </div>

          <div class="mb-4">
            <label class="nd-label">Microsoft Object ID (OID)</label>
            <div class="nd-input-group">
                <input type="text" name="oid" class="nd-input" 
                    value="<?= htmlspecialchars($old['oid'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                    required style="padding-left: 2.5rem;">
                <i class="bi bi-key nd-input-icon"></i>
            </div>
            <div class="form-text small mt-1">ID do objeto do usuário no Azure Active Directory.</div>
          </div>

          <div class="mb-4">
            <label class="nd-label">Tenant ID (opcional)</label>
            <input type="text" name="tenant" class="nd-input" 
                value="<?= htmlspecialchars($old['tenant'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Opcional">
          </div>

          <div class="mb-4">
            <label class="nd-label">User Principal Name (UPN) (opcional)</label>
            <input type="text" name="upn" class="nd-input" 
                value="<?= htmlspecialchars($old['upn'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                placeholder="usuario@dominio.onmicrosoft.com">
          </div>

          <div class="d-flex justify-content-end pt-2 border-top">
             <button type="submit" class="nd-btn nd-btn-primary">
                <i class="bi bi-link me-2"></i> Vincular Conta
             </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
