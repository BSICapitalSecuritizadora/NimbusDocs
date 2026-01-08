<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $user */
?>
<h1 class="h4 mb-3">Gerenciar Cadastro do Usuário</h1>

<div class="card">
    <div class="card-body">
        <form method="post" action="/admin/portal-users/<?= (int)$user['id'] ?>">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">

            <div class="mb-3">
                <label class="form-label">Nome do Usuário</label>
                <input type="text"
                    name="full_name"
                    value="<?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES) ?>"
                    class="form-control form-control-sm <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['full_name'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['full_name'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email"
                    name="email"
                    value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>"
                    class="form-control form-control-sm <?= isset($errors['email']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['email'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">CPF</label>
                <input type="text"
                    name="document"
                    value="<?= htmlspecialchars($user['document'] ?? '', ENT_QUOTES) ?>"
                    class="form-control form-control-sm <?= isset($errors['document']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['document'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['document'], ENT_QUOTES) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                    <?= ((int)($user['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">
                    Cadastro Ativo
                </label>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/admin/portal-users" class="btn btn-sm btn-outline-secondary">Voltar</a>
                <button type="submit" class="btn btn-sm btn-primary">Atualizar</button>
            </div>
        </form>
    </div>
</div>