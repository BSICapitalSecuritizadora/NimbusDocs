<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */
?>
<h1 class="h4 mb-3">Novo Cadastro de Titular</h1>

<div class="card">
    <div class="card-body">
        <form method="post" action="/admin/portal-users">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">

            <div class="mb-3">
                <label class="form-label">Nome/label>
                <input type="text"
                    name="full_name"
                    value="<?= htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES) ?>"
                    class="form-control form-control-sm <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['full_name'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['full_name'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail de Contato</label>
                <input type="email"
                    name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>"
                    class="form-control form-control-sm <?= isset($errors['email']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['email'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">CPF ou CNPJ</label>
                <input type="text"
                    name="document"
                    value="<?= htmlspecialchars($old['document'] ?? '', ENT_QUOTES) ?>"
                    class="form-control form-control-sm <?= isset($errors['document']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['document'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['document'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                    <?= ($old['is_active'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">
                    Cadastro Ativo
                </label>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/admin/portal-users" class="btn btn-sm btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>