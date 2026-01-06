<?php

/** @var array $user */
/** @var string $csrfToken */
/** @var array $errors */
/** @var array $flash */

$errors = $errors ?? [];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-pencil-square text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Editar Administrador</h1>
            <p class="text-muted mb-0 small">Atualize as informações do administrador #<?= (int)$user['id'] ?></p>
        </div>
    </div>
    <a href="/admin/admin-users" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<?php if (!empty($flash['error'])): ?>
    <div class="nd-alert nd-alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados Cadastrais</h5>
            </div>

            <div class="nd-card-body">
                <form method="post" action="/admin/admin-users/<?= (int)$user['id'] ?>/update">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row gx-3 gy-2">
                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="full_name">Nome Completo</label>
                            <div class="nd-input-group">
                                <input
                                    type="text"
                                    class="nd-input <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                    id="full_name"
                                    name="full_name"
                                    value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                    maxlength="190"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-person nd-input-icon"></i>
                            </div>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="email">E-mail</label>
                            <div class="nd-input-group">
                                <input
                                    type="email"
                                    class="nd-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    id="email"
                                    name="email"
                                    value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                    maxlength="190"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-envelope nd-input-icon"></i>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text small text-muted ms-1">
                                Se o e-mail for alterado, pode ser necessário re-autenticar.
                            </div>
                        </div>
                    </div>

                    <div class="row gx-3 gy-2">
                        <div class="col-md-6 mb-3">
                            <label class="nd-label" for="role">Perfil</label>
                            <select class="nd-input form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" name="role" required>
                                <option value="ADMIN" <?= ($user['role'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>
                                    Administrador
                                </option>
                                <option value="SUPER_ADMIN" <?= ($user['role'] ?? '') === 'SUPER_ADMIN' ? 'selected' : '' ?>>
                                    Super Administrador
                                </option>
                            </select>
                            <?php if (isset($errors['role'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text small text-muted ms-1">
                                Super Administradores têm acesso total ao sistema.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3 d-flex align-items-center">
                            <div class="form-check nd-form-check mt-3">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    <?= ($user['status'] ?? 'ACTIVE') === 'ACTIVE' ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark fw-medium" for="is_active">
                                    Usuário Ativo
                                </label>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    Desmarque para suspender o acesso.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 my-3 rounded" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-key-fill me-2" style="color: var(--nd-navy-500);"></i>
                            <h6 class="fw-bold mb-0 text-dark">Segurança</h6>
                        </div>

                        <div class="row gx-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="nd-label" for="password">Nova Senha (opcional)</label>
                                <input
                                    type="password"
                                    class="nd-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password"
                                    name="password"
                                    minlength="8"
                                    maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="******">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="nd-label" for="password_confirmation">Confirmar Nova Senha</label>
                                <input
                                    type="password"
                                    class="nd-input"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    minlength="8"
                                    maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="******">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="/admin/admin-users" class="nd-btn nd-btn-outline">
                            Cancelar
                        </a>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informações do Registro -->
        <div class="nd-card bg-light">
            <div class="nd-card-body py-3">
                <h6 class="nd-card-title small text-muted mb-3 border-bottom pb-2">Informações do Registro</h6>
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Criado em:</div>
                    <div class="col-sm-8 text-dark">
                        <?= $user['created_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['created_at'])), ENT_QUOTES, 'UTF-8') : '-' ?>
                    </div>

                    <div class="col-sm-4 text-muted">Última atualização:</div>
                    <div class="col-sm-8 text-dark">
                        <?= $user['updated_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['updated_at'])), ENT_QUOTES, 'UTF-8') : '-' ?>
                    </div>

                    <div class="col-sm-4 text-muted">Último login:</div>
                    <div class="col-sm-8 text-dark">
                        <?= $user['last_login_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['last_login_at'])), ENT_QUOTES, 'UTF-8') : 'Nunca' ?>
                    </div>

                    <?php if (!empty($user['last_login_provider'])): ?>
                        <div class="col-sm-4 text-muted">Método de login:</div>
                        <div class="col-sm-8 text-dark">
                            <?= htmlspecialchars($user['last_login_provider'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
