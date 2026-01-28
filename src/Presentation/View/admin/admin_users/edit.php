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

<div class="row">
    <!-- Main Form Column -->
    <div class="col-lg-8">
        <form method="post" action="/admin/admin-users/<?= (int)$user['id'] ?>">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="nd-card mb-4">
                <div class="nd-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill" style="color: var(--nd-gold-500);"></i>
                    <h5 class="nd-card-title mb-0">Informações do Usuário</h5>
                </div>

                <div class="nd-card-body">
                    <div class="row gx-3 gy-2">
                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="full_name">Nome do Responsável</label>
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
                            <label class="nd-label" for="email">E-mail Corporativo</label>
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
                                Se o e-mail for alterado, o usuário precisará usar o novo endereço para login.
                            </div>
                        </div>
                    </div>

                    <div class="row gx-3 gy-2">
                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="role">Nível de Acesso</label>
                            <div class="nd-input-group">
                                <select class="nd-input form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" id="role" name="role" required style="padding-left: 2.5rem;">
                                    <option value="ADMIN" <?= ($user['role'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>
                                        Administrador
                                    </option>
                                    <option value="SUPER_ADMIN" <?= ($user['role'] ?? '') === 'SUPER_ADMIN' ? 'selected' : '' ?>>
                                        Administrador Master
                                    </option>
                                </select>
                                <i class="bi bi-shield-lock nd-input-icon"></i>
                            </div>
                            <?php if (isset($errors['role'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text small text-muted ms-1">
                                Permissões avançadas de sistema.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credentials Card -->
            <div class="nd-card mb-4">
                <div class="nd-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-key-fill" style="color: var(--nd-gold-500);"></i>
                    <h5 class="nd-card-title mb-0">Credenciais de Acesso</h5>
                </div>
                <div class="nd-card-body">
                     <div class="p-3 mb-3 rounded" style="background: var(--nd-gray-50); border-left: 3px solid var(--nd-warning);">
                        <small class="text-muted">Preencha apenas se desejar alterar a senha atual.</small>
                    </div>

                    <div class="row gx-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="nd-label" for="password">Nova Senha</label>
                            <div class="nd-input-group">
                                <input
                                    type="password"
                                    class="nd-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password"
                                    name="password"
                                    minlength="8"
                                    maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="******"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-lock nd-input-icon"></i>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="nd-label" for="password_confirmation">Confirmar Nova Senha</label>
                             <div class="nd-input-group">
                                <input
                                    type="password"
                                    class="nd-input"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    minlength="8"
                                    maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="******"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-lock-fill nd-input-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mb-4">
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

    <!-- Sidebar Column -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h5 class="nd-card-title mb-0">Status da Conta</h5>
            </div>
            <div class="nd-card-body">
                <div class="form-check form-switch p-0 m-0 d-flex justify-content-between align-items-center">
                    <label class="form-check-label text-dark fw-medium" for="is_active">
                        Acesso Ativo
                    </label>
                    <input
                        class="form-check-input ms-0" 
                        type="checkbox" 
                        role="switch" 
                        id="is_active" 
                        name="is_active" 
                        value="1" 
                        <?= ($user['status'] ?? 'ACTIVE') === 'ACTIVE' ? 'checked' : '' ?>
                        style="width: 2.5em; height: 1.25em; cursor: pointer;">
                </div>
                <div class="small text-muted mt-2">
                    Desativar o acesso impedirá que este usuário faça login no painel administrativo imediatamente.
                </div>
            </div>
        </div>

        <!-- Metadata Card -->
        <div class="nd-card bg-light border-0">
            <div class="nd-card-body py-3">
                <h6 class="nd-card-title small text-muted mb-3 border-bottom pb-2">Metadados do Registro</h6>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">ID:</span>
                        <span class="text-dark fw-medium">#<?= (int)$user['id'] ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Criado em:</span>
                        <span class="text-dark">
                            <?= $user['created_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['created_at'])), ENT_QUOTES, 'UTF-8') : '-' ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Atualizado em:</span>
                        <span class="text-dark">
                            <?= $user['updated_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['updated_at'])), ENT_QUOTES, 'UTF-8') : '-' ?>
                        </span>
                    </div>

                    <hr class="my-1 border-secondary-subtle">

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Último login:</span>
                        <span class="text-dark">
                            <?= $user['last_login_at'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($user['last_login_at'])), ENT_QUOTES, 'UTF-8') : 'Nunca' ?>
                        </span>
                    </div>

                    <?php if (!empty($user['last_login_provider'])): ?>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Via:</span>
                        <span class="text-dark fw-medium">
                            <?= htmlspecialchars($user['last_login_provider'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
