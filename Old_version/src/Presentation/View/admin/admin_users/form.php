<?php

/** @var string $mode */
/** @var array|null $user */
/** @var array $errors */
/** @var array $old */
/** @var string $csrfToken */

$isEdit = ($mode === 'edit');
$values = [
    'name'      => $old['name']  ?? ($user['name']  ?? ''),
    'email'     => $old['email'] ?? ($user['email'] ?? ''),
    'auth_mode' => $old['auth_mode'] ?? ($user['auth_mode'] ?? 'LOCAL_ONLY'),
    'role'      => $old['role'] ?? ($user['role'] ?? 'ADMIN'),
    'status'    => $old['status'] ?? ($user['status'] ?? 'ACTIVE'),
];
$action = $isEdit
    ? '/admin/users/' . (int)$user['id']
    : '/admin/users';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">
                <?= $isEdit ? 'Editar Administrador' : 'Novo Administrador' ?>
            </h1>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= $action ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="form-label" for="name">Nome</label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            id="name" name="name" required
                            value="<?= htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            id="email" name="email" required
                            value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="auth_mode">Autenticação</label>
                            <select class="form-select <?= isset($errors['auth_mode']) ? 'is-invalid' : '' ?>"
                                id="auth_mode" name="auth_mode">
                                <option value="LOCAL_ONLY" <?= $values['auth_mode'] === 'LOCAL_ONLY' ? 'selected' : '' ?>>Somente local</option>
                                <option value="MS_ONLY" <?= $values['auth_mode'] === 'MS_ONLY' ? 'selected' : '' ?>>Somente Microsoft</option>
                                <option value="LOCAL_AND_MS" <?= $values['auth_mode'] === 'LOCAL_AND_MS' ? 'selected' : '' ?>>Local e Microsoft</option>
                            </select>
                            <?php if (isset($errors['auth_mode'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['auth_mode'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="role">Perfil</label>
                            <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>"
                                id="role" name="role">
                                <option value="ADMIN" <?= $values['role'] === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                                <option value="SUPER_ADMIN" <?= $values['role'] === 'SUPER_ADMIN' ? 'selected' : '' ?>>Super Admin</option>
                            </select>
                            <?php if (isset($errors['role'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                id="status" name="status">
                                <option value="ACTIVE" <?= $values['status'] === 'ACTIVE' ? 'selected' : '' ?>>Ativo</option>
                                <option value="INACTIVE" <?= $values['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inativo</option>
                                <option value="BLOCKED" <?= $values['status'] === 'BLOCKED' ? 'selected' : '' ?>>Bloqueado</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <?= $isEdit ? 'Nova senha (opcional)' : 'Senha' ?>
                        </label>
                        <input type="password"
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            id="password" name="password"
                            <?= $isEdit ? '' : 'required' ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">
                            Confirmar senha<?= $isEdit ? ' (se informada)' : '' ?>
                        </label>
                        <input type="password"
                            class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                            id="password_confirmation" name="password_confirmation"
                            <?= $isEdit ? '' : 'required' ?>>
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <?= $isEdit ? 'Salvar alterações' : 'Cadastrar administrador' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>