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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-lock-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">
                <?= $isEdit ? 'Editar Administrador' : 'Novo Administrador' ?>
            </h1>
            <p class="text-muted mb-0 small">
                <?= $isEdit ? 'Atualize as informações do administrador' : 'Cadastre um novo administrador no sistema' ?>
            </p>
        </div>
    </div>
    <a href="/admin/users" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados Cadastrais</h5>
            </div>
            
            <div class="nd-card-body">
                <form method="post" action="<?= $action ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Basic Info -->
                    <div class="row gx-3 gy-2">
                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="name">Nome Completo</label>
                            <div class="nd-input-group">
                                <input type="text" class="nd-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                    id="name" name="name" required
                                    placeholder="Ex: João da Silva"
                                    value="<?= htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-person nd-input-icon"></i>
                            </div>
                            <?php if (isset($errors['name'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="email">E-mail</label>
                            <div class="nd-input-group">
                                <input type="email" class="nd-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    id="email" name="email" required
                                    placeholder="Ex: joao@empresa.com"
                                    value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8') ?>"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-envelope nd-input-icon"></i>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row gx-3 gy-2">
                        <div class="col-md-4 mb-3">
                            <label class="nd-label" for="auth_mode">Autenticação</label>
                            <select class="nd-input form-select <?= isset($errors['auth_mode']) ? 'is-invalid' : '' ?>"
                                id="auth_mode" name="auth_mode">
                                <option value="LOCAL_ONLY" <?= $values['auth_mode'] === 'LOCAL_ONLY' ? 'selected' : '' ?>>Somente local</option>
                                <option value="MS_ONLY" <?= $values['auth_mode'] === 'MS_ONLY' ? 'selected' : '' ?>>Somente Microsoft</option>
                                <option value="LOCAL_AND_MS" <?= $values['auth_mode'] === 'LOCAL_AND_MS' ? 'selected' : '' ?>>Local e Microsoft</option>
                            </select>
                            <?php if (isset($errors['auth_mode'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['auth_mode'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nd-label" for="role">Perfil</label>
                            <select class="nd-input form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>"
                                id="role" name="role">
                                <option value="ADMIN" <?= $values['role'] === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                                <option value="SUPER_ADMIN" <?= $values['role'] === 'SUPER_ADMIN' ? 'selected' : '' ?>>Super Admin</option>
                            </select>
                            <?php if (isset($errors['role'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="nd-label" for="status">Status</label>
                            <select class="nd-input form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                id="status" name="status">
                                <option value="ACTIVE" <?= $values['status'] === 'ACTIVE' ? 'selected' : '' ?>>Ativo</option>
                                <option value="INACTIVE" <?= $values['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inativo</option>
                                <option value="BLOCKED" <?= $values['status'] === 'BLOCKED' ? 'selected' : '' ?>>Bloqueado</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="p-3 my-3 rounded" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-key-fill me-2" style="color: var(--nd-navy-500);"></i>
                            <h6 class="fw-bold mb-0 text-dark">Segurança</h6>
                        </div>
                        
                        <div class="row gx-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="nd-label" for="password">
                                    <?= $isEdit ? 'Nova senha (opcional)' : 'Senha' ?>
                                </label>
                                <input type="password"
                                    class="nd-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password" name="password"
                                    <?= $isEdit ? '' : 'required' ?>
                                    placeholder="******">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label class="nd-label" for="password_confirmation">
                                    Confirmar senha<?= $isEdit ? ' (se informada)' : '' ?>
                                </label>
                                <input type="password"
                                    class="nd-input <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                                    id="password_confirmation" name="password_confirmation"
                                    <?= $isEdit ? '' : 'required' ?>
                                    placeholder="******">
                                <?php if (isset($errors['password_confirmation'])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Salvar alterações' : 'Cadastrar administrador' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>