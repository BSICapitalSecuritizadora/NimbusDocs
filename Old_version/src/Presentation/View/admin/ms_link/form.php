<?php
/** @var string $csrfToken */
/** @var string|null $error */
/** @var string|null $success */
/** @var array $old */
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card">
      <div class="card-body">
        <h1 class="h5 mb-3">Vincular Conta Microsoft ao Admin</h1>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger small mb-3"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
          <div class="alert alert-success small mb-3"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="/admin/ms-link">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3">
            <label class="form-label">E-mail do Admin</label>
            <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Microsoft Object ID (OID)</label>
            <input type="text" name="oid" class="form-control form-control-sm" value="<?= htmlspecialchars($old['oid'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Tenant ID (opcional)</label>
            <input type="text" name="tenant" class="form-control form-control-sm" value="<?= htmlspecialchars($old['tenant'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">User Principal Name (UPN) (opcional)</label>
            <input type="text" name="upn" class="form-control form-control-sm" value="<?= htmlspecialchars($old['upn'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <button type="submit" class="btn btn-primary btn-sm">Vincular</button>
        </form>
      </div>
    </div>
  </div>
</div>
