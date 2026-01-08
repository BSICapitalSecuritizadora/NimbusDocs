<?php

/** @var string $mode */
/** @var array|null $user */
/** @var array $errors */
/** @var array $old */
/** @var string $csrfToken */
/** @var array $tokens (apenas no modo edit, pode não existir no create) */

$tokens = $tokens ?? [];
$isEdit = ($mode === 'edit');

/* 
   Recupera valores:
   1. 'old' (flash session se houve erro de validação)
   2. 'user' (se for edição e não tiver old)
   3. string vazia (se for criação e não tiver old)
*/
$values = [
    'full_name'       => $old['full_name']       ?? ($user['full_name']       ?? ''),
    'email'           => $old['email']           ?? ($user['email']           ?? ''),
    'document_number' => $old['document_number'] ?? ($user['document_number'] ?? ''),
    'phone_number'    => $old['phone_number']    ?? ($user['phone_number']    ?? ''),
    'status'          => $old['status']          ?? ($user['status']          ?? 'INVITED'),
];

$action = $isEdit
    ? '/admin/portal-users/' . (int)$user['id']
    : '/admin/portal-users';

$pageTitleText = $isEdit ? 'Editar Dados do Usuário' : 'Novo Usuário';
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi <?= $isEdit ? 'bi-person-gear' : 'bi-person-plus-fill' ?> text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);"><?= $pageTitleText ?></h1>
            <p class="text-muted mb-0 small">
                <?= $isEdit ? 'Atualize as informações cadastrais do usuário' : 'Registro de novo usuário externo' ?>
            </p>
        </div>
    </div>
    <a href="/admin/portal-users" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Main Form Card -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-vcard text-primary"></i>
                <h5 class="nd-card-title mb-0">Dados Cadastrais</h5>
            </div>
            <div class="nd-card-body">
                <form method="post" action="<?= $action ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="nd-label" for="full_name">Nome <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text"
                                class="nd-input <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                id="full_name" name="full_name" required
                                placeholder="Ex: João da Silva"
                                value="<?= htmlspecialchars($values['full_name'], ENT_QUOTES, 'UTF-8') ?>"
                                style="padding-left: 1rem;">
                        </div>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="nd-label" for="email">E-mail</label>
                        <div class="nd-input-group">
                            <input type="email"
                                class="nd-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="email" name="email"
                                placeholder="Ex: joao@email.com"
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="nd-label" for="document_number">CPF</label>
                            <input type="text" class="nd-input <?= isset($errors['document_number']) ? 'is-invalid' : '' ?>"
                                id="document_number" name="document_number"
                                placeholder="000.000.000-00"
                                maxlength="14"
                                data-mask="cpf"
                                value="<?= htmlspecialchars($values['document_number'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['document_number'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= htmlspecialchars($errors['document_number'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="nd-label" for="phone_number">Telefone/Celular</label>
                            <div class="nd-input-group">
                                <input type="text" class="nd-input"
                                    id="phone_number" name="phone_number"
                                    placeholder="(00) 00000-0000"
                                    maxlength="15"
                                    data-mask="phone"
                                    value="<?= htmlspecialchars($values['phone_number'], ENT_QUOTES, 'UTF-8') ?>"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-telephone nd-input-icon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="nd-label" for="status">Situação Cadastral</label>
                        <div class="nd-input-group">
                            <select class="nd-input <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                id="status" name="status" style="padding-left: 1rem;">
                                <option value="ACTIVE" <?= $values['status'] === 'ACTIVE'   ? 'selected' : '' ?>>Ativo</option>
                                <option value="INVITED" <?= $values['status'] === 'INVITED'  ? 'selected' : '' ?>>Aguardando Cadastro</option>
                                <option value="INACTIVE" <?= $values['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inativo</option>
                                <option value="BLOCKED" <?= $values['status'] === 'BLOCKED'  ? 'selected' : '' ?>>Suspenso</option>
                            </select>
                        </div>
                        <?php if (isset($errors['status'])): ?>
                            <div class="text-danger small mt-1">
                                <?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="/admin/portal-users" class="nd-btn nd-btn-outline">Cancelar</a>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Salvar Alterações' : 'Confirmar Cadastro' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tokens Section (Edit Only) -->
        <?php if ($isEdit): ?>
            <div class="nd-card">
                <div class="nd-card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-key text-warning"></i>
                        <h5 class="nd-card-title mb-0">Credenciais Temporárias</h5>
                    </div>
                </div>
                
                <div class="nd-card-body">
                    <!-- Generate Token Form -->
                    <div class="p-3 mb-4 rounded" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                        <form method="post" action="/admin/portal-users/<?= (int)$user['id'] ?>/tokens" class="row g-3 align-items-end">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                            <div class="col-sm-8">
                                <label class="nd-label mb-1" for="validity">Válido por</label>
                                <div class="nd-input-group">
                                    <select class="nd-input" id="validity" name="validity" style="padding-left: 1rem;">
                                        <option value="1h">1 hora</option>
                                        <option value="24h" selected>24 horas</option>
                                        <option value="7d">7 dias</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="nd-btn nd-btn-gold w-100">
                                    <i class="bi bi-magic me-1"></i> Gerar Código
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tokens List -->
                    <h6 class="fw-semibold text-dark mb-3">Histórico de códigos gerados</h6>
                    
                    <?php if (!$tokens): ?>
                        <div class="text-center py-4 text-muted border rounded bg-light">
                            <small>Nenhum código gerado para este usuário.</small>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="nd-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Status</th>
                                        <th>Criação</th>
                                        <th>Expira em</th>
                                        <th>Usado em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tokens as $t): ?>
                                        <?php
                                            $isUsed = !empty($t['used_at']);
                                            $isExpired = strtotime($t['expires_at']) < time();
                                            $statusBadge = '';
                                            
                                            // Lógica visual de status do token
                                            if ($t['status'] === 'REVOKED') {
                                                $statusBadge = '<span class="nd-badge nd-badge-danger">Revogado</span>';
                                            } elseif ($t['status'] === 'USED' || $isUsed) {
                                                $statusBadge = '<span class="nd-badge nd-badge-success">Usado</span>';
                                            } elseif ($isExpired) {
                                                 $statusBadge = '<span class="nd-badge nd-badge-secondary">Expirado</span>';
                                            } else {
                                                $statusBadge = '<span class="nd-badge nd-badge-warning">Pendente</span>';
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <code class="px-2 py-1 rounded small bg-light text-dark border fw-bold" style="letter-spacing: 0.5px;">
                                                    <?= htmlspecialchars($t['code'], ENT_QUOTES, 'UTF-8') ?>
                                                </code>
                                            </td>
                                            <td><?= $statusBadge ?></td>
                                            <td class="small text-muted">
                                                <?= (new DateTime($t['created_at']))->format('d/m/Y H:i') ?>
                                            </td>
                                            <td class="small text-muted">
                                                <?= (new DateTime($t['expires_at']))->format('d/m/Y H:i') ?>
                                            </td>
                                            <td class="small">
                                                <?= $isUsed ? (new DateTime($t['used_at']))->format('d/m/Y H:i') : '-' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>