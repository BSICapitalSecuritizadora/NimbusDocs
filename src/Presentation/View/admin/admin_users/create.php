<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-person-plus-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Novo Administrador</h1>
            <p class="text-muted mb-0 small">Preencha os dados abaixo para conceder acesso administrativo.</p>
        </div>
    </div>
    <a href="/admin/admin-users" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Cancelar
    </a>
</div>

<div class="row">
    <!-- Main Form Column -->
    <div class="col-lg-8">
        <form method="post" action="/admin/admin-users">
            <input type="hidden" name="_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

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
                                <input type="text"
                                    class="nd-input <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                    id="full_name" name="full_name"
                                    placeholder="Ex: Maria Souza"
                                    value="<?= htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                                <input type="email"
                                    class="nd-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    id="email" name="email"
                                    placeholder="Ex: maria@empresa.com"
                                    value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="role">Nível de Acesso</label>
                            <div class="nd-input-group">
                                <select class="nd-input form-select" id="role" name="role" style="padding-left: 2.5rem;">
                                    <?php
                                    $role = $old['role'] ?? 'ADMIN';
                                    ?>
                                    <option value="SUPER_ADMIN" <?= $role === 'SUPER_ADMIN' ? 'selected' : '' ?>>Administrador Master</option>
                                    <option value="ADMIN" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="AUDITOR" <?= $role === 'AUDITOR' ? 'selected' : '' ?>>Apenas Leitura (Auditor)</option>
                                </select>
                                <i class="bi bi-shield-lock nd-input-icon"></i>
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
                    <div class="p-3 mb-3 rounded" style="background: var(--nd-gray-50); border-left: 3px solid var(--nd-primary);">
                        <small class="text-muted">Defina uma senha segura inicial para o novo usuário.</small>
                    </div>

                    <div class="row gx-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="nd-label" for="password">Senha</label>
                            <div class="nd-input-group">
                                <input type="password"
                                    class="nd-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password" name="password"
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
                            <label class="nd-label" for="password_confirmation">Confirmar Senha</label>
                            <div class="nd-input-group">
                                <input type="password"
                                    class="nd-input"
                                    id="password_confirmation" name="password_confirmation"
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
                    Cadastrar Administrador
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
                    <input class="form-check-input ms-0" 
                        type="checkbox" 
                        role="switch"
                        id="is_active" name="is_active"
                        <?= ($old['is_active'] ?? 1) ? 'checked' : '' ?>
                        style="width: 2.5em; height: 1.25em; cursor: pointer;">
                </div>
                <div class="small text-muted mt-2">
                    Desativar o acesso impedirá que este usuário faça login no painel administrativo.
                </div>
            </div>
        </div>
    </div>
</div>