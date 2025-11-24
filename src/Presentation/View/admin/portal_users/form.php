<?php

/** @var string $mode */
/** @var array|null $user */
/** @var array $errors */
/** @var array $old */
/** @var string $csrfToken */

$isEdit = ($mode === 'edit');
$values = [
    'full_name'       => $old['full_name']       ?? ($user['full_name']       ?? ''),
    'email'           => $old['email']           ?? ($user['email']           ?? ''),
    'document_number' => $old['document_number'] ?? ($user['document_number'] ?? ''),
    'phone_number'    => $old['phone_number']    ?? ($user['phone_number']    ?? ''),
    'external_id'     => $old['external_id']     ?? ($user['external_id']     ?? ''),
    'notes'           => $old['notes']           ?? ($user['notes']           ?? ''),
    'status'          => $old['status']          ?? ($user['status']          ?? 'INVITED'),
];

$action = $isEdit
    ? '/admin/portal-users/' . (int)$user['id']
    : '/admin/portal-users';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">
                <?= $isEdit ? 'Editar Usuário Final' : 'Novo Usuário Final' ?>
            </h1>
            <a href="/admin/portal-users" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= $action ?>">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="form-label" for="full_name">Nome completo</label>
                        <input type="text"
                            class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                            id="full_name" name="full_name" required
                            value="<?= htmlspecialchars($values['full_name'], ENT_QUOTES, 'UTF-8') ?>">
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
                            value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="document_number">Documento</label>
                            <input type="text" class="form-control"
                                id="document_number" name="document_number"
                                value="<?= htmlspecialchars($values['document_number'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone_number">Telefone</label>
                            <input type="text" class="form-control"
                                id="phone_number" name="phone_number"
                                value="<?= htmlspecialchars($values['phone_number'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="external_id">ID externo (ERP/CRM)</label>
                        <input type="text" class="form-control"
                            id="external_id" name="external_id"
                            value="<?= htmlspecialchars($values['external_id'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($values['notes'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                            id="status" name="status">
                            <option value="INVITED" <?= $values['status'] === 'INVITED'  ? 'selected' : '' ?>>Convidado</option>
                            <option value="ACTIVE" <?= $values['status'] === 'ACTIVE'   ? 'selected' : '' ?>>Ativo</option>
                            <option value="INACTIVE" <?= $values['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inativo</option>
                            <option value="BLOCKED" <?= $values['status'] === 'BLOCKED'  ? 'selected' : '' ?>>Bloqueado</option>
                        </select>
                        <?php if (isset($errors['status'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <?= $isEdit ? 'Salvar alterações' : 'Cadastrar usuário' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>