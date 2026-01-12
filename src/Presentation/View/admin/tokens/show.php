<?php

/** @var array $token */
$isUsed    = !empty($token['used_at']);
$isExpired = !$isUsed && (strtotime($token['expires_at']) < time());
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-qr-code text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Detalhes da Credencial</h1>
            <p class="text-muted mb-0 small">Visão detalhada da credencial de acesso #<?= (int)$token['id'] ?></p>
        </div>
    </div>
    <a href="/admin/tokens" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados da Credencial</h5>
            </div>
            
            <div class="nd-card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small text-uppercase fw-bold pt-1">Titular</div>
                    <div class="col-sm-8">
                        <div class="fw-medium text-dark"><?= htmlspecialchars($token['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($token['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small text-uppercase fw-bold pt-1">Código de Autenticação</div>
                    <div class="col-sm-8">
                        <?php $code = (string)($token['code'] ?? ''); ?>
                        <?php if ($code !== ''): ?>
                            <div class="p-2 bg-light rounded border d-inline-block font-monospace text-break" style="color: var(--nd-navy-600);">
                                <?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-3" style="border-color: var(--nd-gray-200);">

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small text-uppercase fw-bold pt-1">Data de Emissão</div>
                    <div class="col-sm-8 text-dark">
                        <i class="bi bi-calendar-plus me-1 text-muted"></i>
                        <?= (new DateTime($token['created_at']))->format('d/m/Y H:i') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small text-uppercase fw-bold pt-1">Data de Expiração</div>
                    <div class="col-sm-8 text-dark">
                        <i class="bi bi-calendar-event me-1 text-muted"></i>
                        <?= (new DateTime($token['expires_at']))->format('d/m/Y H:i') ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small text-uppercase fw-bold pt-1">Utilizado em</div>
                    <div class="col-sm-8 text-dark">
                        <?php if ($token['used_at']): ?>
                            <i class="bi bi-check-circle me-1 text-success"></i>
                            <?= (new DateTime($token['used_at']))->format('d/m/Y H:i') ?>
                        <?php else: ?>
                            <span class="text-muted">Ainda não utilizado</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-sm-4 text-muted small text-uppercase fw-bold pt-1">Situação Atual</div>
                    <div class="col-sm-8">
                        <?php if ($isUsed): ?>
                            <span class="nd-badge nd-badge-secondary">Utilizado/Revogado</span>
                        <?php elseif ($isExpired): ?>
                            <span class="nd-badge nd-badge-danger">Expirado</span>
                        <?php else: ?>
                            <span class="nd-badge nd-badge-success">Ativo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Sidebar (Optional) -->
    <?php if (!$isUsed && !$isExpired): ?>
        <div class="col-lg-4">
            <div class="nd-card border-danger">
                <div class="nd-card-header bg-danger text-white d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h5 class="nd-card-title mb-0 text-white">Zona de Revogação</h5>
                </div>
                <div class="nd-card-body">
                    <p class="small text-muted mb-3">
                        Revogar esta credencial impedirá que o usuário a utilize para acessar o sistema. Esta ação é irreversível.
                    </p>
                    <form method="post" action="/admin/tokens/<?= (int)$token['id'] ?>/revoke"
                        onsubmit="return confirm('Tem certeza que deseja revogar esta credencial?');">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- CSRF Token might need to be passed from controller if strictly required, usually available in layout scope but better safe -->
                        <button type="submit" class="nd-btn nd-btn-outline w-100 text-danger border-danger">
                            <i class="bi bi-x-circle me-2"></i> Revogar Credencial
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>