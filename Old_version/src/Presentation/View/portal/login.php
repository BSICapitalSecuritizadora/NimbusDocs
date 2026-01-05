<?php

/** @var string $csrfToken */
/** @var array $flash */
$error   = $flash['error']   ?? null;
$success = $flash['success'] ?? null;
$oldIdentifier = $flash['old_identifier'] ?? '';
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h1 class="h4 mb-3 text-center">Acesso ao Portal</h1>
                <p class="text-muted small text-center mb-3">
                    Acesse usando apenas o código de acesso enviado pelo administrador.
                </p>

                <?php if ($error): ?>
                    <div class="alert alert-danger py-2">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success py-2">
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="/portal/login" class="row g-3">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="col-12">
                        <label for="access_code" class="form-label">Código de acesso</label>
                        <input type="text"
                            class="form-control"
                            id="access_code"
                            name="access_code"
                            autocomplete="one-time-code">
                        <div class="form-text">
                            Ex.: ABCD2345EFGH (sem diferenciar maiúsculas/minúsculas).
                        </div>
                    </div>

                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-primary">
                            Entrar no portal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>