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
            <p class="text-muted mb-0 small">Cadastre um novo administrador no sistema</p>
        </div>
    </div>
    <a href="/admin/admin-users" class="nd-btn nd-btn-outline nd-btn-sm">
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
                <form method="post" action="/admin/admin-users">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row gx-3 gy-2">
                        <div class="col-md-12 mb-3">
                            <label class="nd-label" for="full_name">Nome completo</label>
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
                            <label class="nd-label" for="email">E-mail</label>
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
                        <div class="col-md-6 mb-3">
                            <label class="nd-label" for="role">Papel</label>
                            <select class="nd-input form-select" id="role" name="role">
                                <?php
                                $role = $old['role'] ?? 'ADMIN';
                                ?>
                                <option value="SUPER_ADMIN" <?= $role === 'SUPER_ADMIN' ? 'selected' : '' ?>>Super Admin</option>
                                <option value="ADMIN" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                                <option value="AUDITOR" <?= $role === 'AUDITOR' ? 'selected' : '' ?>>Auditor</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check nd-form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    <?= ($old['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark fw-medium" for="is_active">
                                    Usuário Ativo
                                </label>
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
                                <label class="nd-label" for="password">Senha</label>
                                <input type="password"
                                    class="nd-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password" name="password"
                                    placeholder="******">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="nd-label" for="password_confirmation">Confirme a senha</label>
                                <input type="password"
                                    class="nd-input"
                                    id="password_confirmation" name="password_confirmation"
                                    placeholder="******">
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>