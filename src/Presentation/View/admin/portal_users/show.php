<?php

/** @var array $user */
/** @var array $tokens */
/** @var string $csrfToken */

// Formata CPF/CNPJ
$doc = $user['document_number'] ?? '';
$len = strlen($doc);
if ($len === 11) {
    $doc = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
} elseif ($len === 14) {
    $doc = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-person-badge-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes do Usuário</h1>
            <p class="text-muted mb-0 small">Visão geral do cadastro e credenciais</p>
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

<div class="row">
    <!-- Main Content Column -->
    <div class="col-lg-8">
        <!-- User Info Card -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-vcard" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados Cadastrais</h5>
            </div>
            <div class="nd-card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="nd-label text-muted mb-1">Nome do Usuário</label>
                        <div class="d-flex align-items-center gap-2">
                             <div class="nd-avatar nd-avatar-sm bg-light text-primary fw-bold border">
                                <?= strtoupper(substr($user['full_name'] ?? $user['name'] ?? 'U', 0, 2)) ?>
                            </div>
                            <div class="fs-6 fw-bold text-dark">
                                <?= htmlspecialchars($user['full_name'] ?? $user['name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">E-mail</label>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope text-primary small"></i>
                            <span class="text-dark"><?= htmlspecialchars($user['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Telefone/Celular</label>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone text-primary small"></i>
                            <span class="text-dark"><?= htmlspecialchars($user['phone_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">CPF</label>
                        <div>
                            <?php if (!empty($doc)): ?>
                                <code class="px-2 py-1 rounded bg-light text-dark border">
                                    <i class="bi bi-card-heading me-1 text-muted small"></i>
                                    <?= htmlspecialchars($doc, ENT_QUOTES, 'UTF-8') ?>
                                </code>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credentials History -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-clock-history" style="color: var(--nd-navy-500);"></i>
                <h5 class="nd-card-title mb-0">Histórico de Acesso</h5>
            </div>
            <div class="nd-card-body">
                 <?php if (!$tokens): ?>
                    <div class="text-center py-4 border rounded bg-light">
                        <i class="bi bi-link-45deg text-muted mb-2" style="font-size: 1.5rem;"></i>
                        <p class="text-muted mb-0 small">Nenhuma credencial gerada até o momento.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="nd-table">
                            <thead>
                                <tr>
                                    <th>Criação</th>
                                    <th>Validade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tokens as $t): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-calendar2-check text-muted small"></i>
                                                <?= (new DateTime($t['created_at']))->format('d/m/Y H:i') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                Até <?= (new DateTime($t['expires_at']))->format('d/m/Y H:i') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($t['used_at']): ?>
                                                <div class="d-flex align-items-center gap-1 text-success small fw-medium">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    <span>Usado em <?= (new DateTime($t['used_at']))->format('d/m/H:i') ?></span>
                                                </div>
                                            <?php else: ?>
                                                <?php 
                                                    $isExpired = strtotime($t['expires_at']) < time();
                                                    if ($isExpired): 
                                                ?>
                                                    <span class="nd-badge nd-badge-secondary">Expirado</span>
                                                <?php else: ?>
                                                    <span class="nd-badge nd-badge-warning">Pendente</span>
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

    <!-- Sidebar Column -->
    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h5 class="nd-card-title mb-0">Ações Rápidas</h5>
            </div>
            <div class="nd-card-body d-flex flex-column gap-2">
                <a href="/admin/portal-users/<?= (int)$user['id'] ?>/edit" class="nd-btn nd-btn-outline w-100 justify-content-center">
                    <i class="bi bi-pencil me-2"></i> Editar Dados
                </a>
                
                <form method="post" action="/admin/portal-users/<?= (int)$user['id'] ?>/access-link" class="w-100">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="nd-btn nd-btn-primary w-100 justify-content-center">
                        <i class="bi bi-magic me-2"></i> Gerar Novo Acesso
                    </button>
                </form>
            </div>
        </div>

        <!-- Status Card -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h5 class="nd-card-title mb-0">Status da Conta</h5>
            </div>
            <div class="nd-card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">Situação Atual:</span>
                     <?php if (($user['status'] ?? '') === 'ACTIVE'): ?>
                        <span class="nd-badge nd-badge-success">Ativo</span>
                    <?php elseif (($user['status'] ?? '') === 'INVITED'): ?>
                        <span class="nd-badge nd-badge-info">Aguardando Cadastro</span>
                    <?php elseif (($user['status'] ?? '') === 'BLOCKED'): ?>
                        <span class="nd-badge nd-badge-danger">Suspenso</span>
                    <?php else: ?>
                        <span class="nd-badge nd-badge-secondary">Inativo</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($user['external_id'])): ?>
                    <div class="d-flex flex-column gap-1 p-2 bg-light rounded border">
                        <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">ID Externo</span>
                        <code class="text-dark"><?= htmlspecialchars($user['external_id'], ENT_QUOTES, 'UTF-8') ?></code>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>