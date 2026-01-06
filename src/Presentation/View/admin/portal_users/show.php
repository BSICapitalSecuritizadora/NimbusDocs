<?php

/** @var array $user */
/** @var array $tokens */
/** @var string $csrfToken */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-person-badge-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes do Usuário</h1>
            <p class="text-muted mb-0 small">Gerencie as informações e acesso</p>
        </div>
    </div>
    <a href="/admin/portal-users" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<!-- Alerts -->
<?php if ($success = \App\Support\Session::getFlash('success')): ?>
    <div class="nd-alert nd-alert-success mb-4" id="alertSuccess">
        <i class="bi bi-check-circle-fill text-success"></i>
        <span class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<?php if ($error = \App\Support\Session::getFlash('error')): ?>
    <div class="nd-alert nd-alert-danger mb-4" id="alertError">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<?php if ($emailError = \App\Support\Session::getFlash('email_error')): ?>
    <div class="nd-alert nd-alert-warning mb-4" id="alertEmail">
        <i class="bi bi-envelope-exclamation-fill"></i>
        <span class="nd-alert-text">
            <strong>Falha ao enviar e-mail:</strong> <?= htmlspecialchars($emailError, ENT_QUOTES, 'UTF-8') ?>
        </span>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertEmail').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- User Info Card -->
    <div class="col-lg-12">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-fill" style="color: var(--nd-gold-500);"></i>
                    <h5 class="nd-card-title mb-0">Informações Pessoais</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="/admin/portal-users/<?= (int)$user['id'] ?>/edit" class="nd-btn nd-btn-outline nd-btn-sm">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    
                    <?php if (($user['status'] ?? '') === 'ACTIVE'): ?>
                        <span class="nd-badge nd-badge-success">Ativo</span>
                    <?php elseif (($user['status'] ?? '') === 'INVITED'): ?>
                        <span class="nd-badge nd-badge-info">Convidado</span>
                    <?php elseif (($user['status'] ?? '') === 'BLOCKED'): ?>
                        <span class="nd-badge nd-badge-danger">Bloqueado</span>
                    <?php else: ?>
                        <span class="nd-badge nd-badge-secondary">Inativo</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="nd-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Nome Completo</label>
                        <div class="fs-6 fw-medium text-dark">
                            <?= htmlspecialchars($user['full_name'] ?? $user['name'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">E-mail</label>
                        <div class="fs-6 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-envelope text-muted small"></i>
                            <?= htmlspecialchars($user['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Documento (CPF/CNPJ)</label>
                        <div class="fs-6 text-dark">
                            <?php if (!empty($user['document_number'])): ?>
                                <code class="px-2 py-1 rounded bg-light text-dark border">
                                    <?= htmlspecialchars($user['document_number'], ENT_QUOTES, 'UTF-8') ?>
                                </code>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Management -->
    <div class="col-lg-12">
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-key-fill" style="color: var(--nd-navy-500);"></i>
                <h5 class="nd-card-title mb-0">Gerenciamento de Acesso</h5>
            </div>
            <div class="nd-card-body">
                <div class="p-4 rounded mb-4" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Link de Acesso Único</h6>
                            <p class="text-muted small mb-0">
                                Gere um novo link mágico para permitir que o usuário acesse o portal sem senha.
                                O link expira em 24 horas.
                            </p>
                        </div>
                        <form method="post" action="/admin/portal-users/<?= (int)$user['id'] ?>/access-link">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="nd-btn nd-btn-primary">
                                <i class="bi bi-magic me-2"></i>Generar e enviar novo link
                            </button>
                        </form>
                    </div>
                </div>

                <h6 class="mb-3 fw-semibold text-dark">Histórico recente de links</h6>
                
                <?php if (!$tokens): ?>
                    <div class="text-center py-4 border rounded bg-light">
                        <i class="bi bi-link-45deg text-muted mb-2" style="font-size: 1.5rem;"></i>
                        <p class="text-muted mb-0 small">Nenhum link gerado ainda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="nd-table">
                            <thead>
                                <tr>
                                    <th>Criado em</th>
                                    <th>Expira em</th>
                                    <th>Status de Uso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tokens as $t): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-clock me-1 text-muted"></i>
                                            <?= htmlspecialchars($t['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($t['expires_at'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <?php if ($t['used_at']): ?>
                                                <div class="d-flex align-items-center gap-2 text-success small fw-medium">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Usado em <?= htmlspecialchars($t['used_at'], ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            <?php else: ?>
                                                <?php 
                                                    $isExpired = strtotime($t['expires_at']) < time();
                                                    if ($isExpired): 
                                                ?>
                                                    <span class="nd-badge nd-badge-secondary">Expirado</span>
                                                <?php else: ?>
                                                    <span class="nd-badge nd-badge-warning">Ainda não usado</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>