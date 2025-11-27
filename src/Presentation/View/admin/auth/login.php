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

        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3 text-center">Acesso Administrativo</h1>
                <p class="text-muted small text-center mb-4">
                    Utilize seu e-mail corporativo e senha de acesso ao NimbusDocs.
                </p>

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger py-2">
                        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="/admin/login">
                    <!-- CSRF -->
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            required
                            autofocus
                            value="<?= htmlspecialchars($oldEmail ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            required>
                    </div>

                    <!-- botão para login com Microsoft -->
                    <div class="mt-3">
                        <a href="/admin/login/microsoft" class="btn btn-outline-secondary w-100">
                            Entrar com Microsoft
                        </a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Entrar
                        </button>
                    </div>

                    <!-- Futuro: link "esqueci minha senha" se você quiser implementar -->
                </form>
            </div>
        </div>

        <p class="text-center text-muted small mt-3">
            Acesso restrito ao departamento administrativo.
        </p>
    </div>
</div>