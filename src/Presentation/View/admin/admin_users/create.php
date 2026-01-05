<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */
?>
<h1 class="h4 mb-3">Novo administrador</h1>

<div class="card">
    <div class="card-body">
        <form method="post" action="/admin/admin-users">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label class="form-label" for="full_name">Nome completo</label>
                <input type="text"
                    class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                    id="full_name" name="full_name"
                    value="<?= htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['full_name'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">E-mail</label>
                <input type="email"
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    id="email" name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="role">Papel</label>
                <select class="form-select" id="role" name="role">
                    <?php
                    $role = $old['role'] ?? 'ADMIN';
                    ?>
                    <option value="SUPER_ADMIN" <?= $role === 'SUPER_ADMIN' ? 'selected' : '' ?>>Super Admin</option>
                    <option value="ADMIN" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                    <option value="AUDITOR" <?= $role === 'AUDITOR' ? 'selected' : '' ?>>Auditor</option>
                </select>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                    <?= ($old['is_active'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">Ativo</label>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Senha (opcional – para login local)</label>
                <input type="password"
                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    id="password" name="password">
            </div>
            <div class="mb-3">
                <label class="form-label" for="password_confirmation">Confirme a senha</label>
                <input type="password"
                    class="form-control"
                    id="password_confirmation" name="password_confirmation">
            </div>

            <div class="d-flex justify-content-between">
                <a href="/admin/admin-users" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>