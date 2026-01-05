<?php

/** @var array $user */
/** @var string $csrfToken */
/** @var array $errors */
/** @var array $flash */

$errors = $errors ?? [];
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Editar Administrador</h1>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger py-2">
                <?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="/admin/users/<?= (int)$user['id'] ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Nome Completo -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            Nome Completo <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                            id="full_name"
                            name="full_name"
                            value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required
                            maxlength="190"
                        >
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- E-mail -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            E-mail <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required
                            maxlength="190"
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            Se o e-mail for alterado, pode ser necessário re-autenticar.
                        </div>
                    </div>

                    <!-- Perfil/Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            Perfil <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>"
                            id="role"
                            name="role"
                            required
                        >
                            <option value="ADMIN" <?= ($user['role'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>
                                Administrador
                            </option>
                            <option value="SUPER_ADMIN" <?= ($user['role'] ?? '') === 'SUPER_ADMIN' ? 'selected' : '' ?>>
                                Super Administrador
                            </option>
                        </select>
                        <?php if (isset($errors['role'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            Super Administradores têm acesso total ao sistema.
                        </div>
                    </div>

                    <!-- Status/Ativo -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="is_active"
                                name="is_active"
                                value="1"
                                <?= ($user['status'] ?? 'ACTIVE') === 'ACTIVE' ? 'checked' : '' ?>
                            >
                            <label class="form-check-label" for="is_active">
                                Usuário ativo
                            </label>
                        </div>
                        <div class="form-text">
                            Desmarque para desativar o acesso deste administrador.
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Senha (opcional para edição) -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Nova Senha (deixe em branco para manter a atual)
                        </label>
                        <input
                            type="password"
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            id="password"
                            name="password"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                        >
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            Mínimo de 8 caracteres.
                        </div>
                    </div>

                    <!-- Confirmação de Senha -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            Confirmar Nova Senha
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                        >
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Salvar Alterações
                        </button>
                        <a href="/admin/users" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informações adicionais -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Informações do Registro</h6>
                <dl class="row mb-0 small">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?= (int)$user['id'] ?></dd>

                    <dt class="col-sm-4">Criado em:</dt>
                    <dd class="col-sm-8">
                        <?= $user['created_at'] ? htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') : '-' ?>
                    </dd>

                    <dt class="col-sm-4">Última atualização:</dt>
                    <dd class="col-sm-8">
                        <?= $user['updated_at'] ? htmlspecialchars($user['updated_at'], ENT_QUOTES, 'UTF-8') : '-' ?>
                    </dd>

                    <dt class="col-sm-4">Último login:</dt>
                    <dd class="col-sm-8">
                        <?= $user['last_login_at'] ? htmlspecialchars($user['last_login_at'], ENT_QUOTES, 'UTF-8') : 'Nunca' ?>
                    </dd>

                    <?php if (!empty($user['last_login_provider'])): ?>
                        <dt class="col-sm-4">Método de login:</dt>
                        <dd class="col-sm-8">
                            <?= htmlspecialchars($user['last_login_provider'], ENT_QUOTES, 'UTF-8') ?>
                        </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</div>
