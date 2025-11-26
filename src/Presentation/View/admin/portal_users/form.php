<?php

/** @var string $mode */
/** @var array|null $user */
/** @var array $errors */
/** @var array $old */
/** @var string $csrfToken */
/** @var array $tokens (apenas no modo edit, pode não existir no create) */

$tokens = $tokens ?? [];

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
                            <label class="form-label" for="document_number">CPF</label>
                            <input type="text" class="form-control <?= isset($errors['document_number']) ? 'is-invalid' : '' ?>"
                                id="document_number" name="document_number"
                                value="<?= htmlspecialchars($values['document_number'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['document_number'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['document_number'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
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
            <?php if ($isEdit): ?>
                <hr class="my-4">

                <h2 class="h5 mb-3">Códigos de acesso</h2>

                <form method="post" action="/admin/portal-users/<?= (int)$user['id'] ?>/tokens" class="row g-2 align-items-end mb-3">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="col-sm-4">
                        <label class="form-label" for="validity">Validade</label>
                        <select class="form-select" id="validity" name="validity">
                            <option value="1h">1 hora</option>
                            <option value="24h" selected>24 horas</option>
                            <option value="7d">7 dias</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            Gerar novo código
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Status</th>
                                <th>Criação</th>
                                <th>Expira em</th>
                                <th>Usado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$tokens): ?>
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-2">
                                        Nenhum código gerado para este usuário.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tokens as $t): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($t['code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                        <td><?= htmlspecialchars($t['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($t['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($t['expires_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= $t['used_at'] ? htmlspecialchars($t['used_at'], ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>