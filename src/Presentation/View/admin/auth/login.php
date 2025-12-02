<?php

/**
 * View: Tela de Login Administrativo
 *
 * Espera (opcionalmente) as variáveis:
 * - string $errorMessage  Mensagem de erro de autenticação (se houver)
 * - string $oldEmail      Último e-mail digitado (para manter no form)
 * - string $csrfToken     Token CSRF para o formulário
 */

?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">

        <div class="card">
            <div class="card-body">
                <h1 class="h5 mb-3">Acesso Administrativo</h1>

                <?php $error = $errorMessage ?? \App\Support\Session::getFlash('error'); ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small mb-3">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="/admin/login" class="mb-3">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <!-- login local -->
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?= htmlspecialchars($oldEmail ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="password" class="form-control form-control-sm" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">Entrar</button>
                </form>

                <div class="text-center my-3">
                    <span class="text-muted small">ou</span>
                </div>

                <a href="/admin/login/microsoft" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-microsoft"></i> Entrar com Microsoft
                </a>
            </div>
        </div>

        <p class="text-center text-muted small mt-3">
            Acesso restrito ao departamento administrativo.
        </p>
    </div>
</div>