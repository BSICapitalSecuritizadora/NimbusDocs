<?php

/** @var array $token */
/** @var string $csrfToken */ 

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
        Voltar para a Lista
    </a>
</div>

<div class="row">
    <!-- Main Column -->
    <div class="col-lg-8">
        <!-- Token Details -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-key-fill" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Informações da Credencial</h5>
            </div>
            
            <div class="nd-card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="nd-label text-muted mb-2">Código de Autenticação</label>
                        <?php $code = (string)($token['code'] ?? ''); ?>
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between flex-grow-1">
                                <code class="fs-5 fw-bold text-dark" style="letter-spacing: 2px;"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></code>
                                <i class="bi bi-clipboard text-muted" style="cursor: pointer;" title="Copiar"></i>
                            </div>
                        </div>
                         <div class="small text-muted mt-2">
                             Este token é único e serve para autenticação direta.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Data de Emissão</label>
                        <div class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-calendar-plus text-primary"></i>
                            <?= (new DateTime($token['created_at']))->format('d/m/Y \à\s H:i') ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Válido até</label>
                         <div class="d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-calendar-event text-danger"></i>
                            <?= (new DateTime($token['expires_at']))->format('d/m/Y \à\s H:i') ?>
                        </div>
                    </div>

                    <div class="col-12 border-top pt-3">
                         <label class="nd-label text-muted mb-2">Histórico de Uso</label>
                         <?php if ($token['used_at']): ?>
                            <div class="alert alert-success d-flex align-items-center gap-3 mb-0" role="alert">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                <div>
                                    <div class="fw-bold">Utilizado com sucesso</div>
                                    <div class="small">Acesso realizado em <?= (new DateTime($token['used_at']))->format('d/m/Y \à\s H:i') ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                             <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-dash-circle"></i> Não utilizado até o momento
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

         <!-- User Details -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-fill" style="color: var(--nd-navy-500);"></i>
                    <h5 class="nd-card-title mb-0">Dados do Titular</h5>
                </div>
                 <a href="/admin/portal-users/<?= (int)($token['portal_user_id'] ?? 0) ?>" class="btn btn-sm btn-link text-decoration-none p-0">
                    Ver Perfil <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="nd-card-body">
                <div class="d-flex align-items-center gap-3">
                     <div class="nd-avatar nd-avatar-lg bg-light text-primary fw-bold border">
                         <?= strtoupper(substr($token['user_name'] ?? 'U', 0, 2)) ?>
                    </div>
                    <div>
                         <div class="fw-bold text-dark fs-5"><?= htmlspecialchars($token['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                         <div class="text-muted d-flex align-items-center gap-2">
                             <i class="bi bi-envelope"></i>
                             <?= htmlspecialchars($token['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h5 class="nd-card-title mb-0">Status da Credencial</h5>
            </div>
            <div class="nd-card-body">
                 <div class="mb-3 text-center py-3 rounded bg-light border">
                    <?php if ($isUsed): ?>
                        <div class="text-success fw-bold d-flex flex-column align-items-center gap-2">
                             <i class="bi bi-check-circle-fill fs-1"></i>
                             <span class="fs-5">UTILIZADA</span>
                        </div>
                    <?php elseif ($isExpired): ?>
                        <div class="text-danger fw-bold d-flex flex-column align-items-center gap-2">
                             <i class="bi bi-x-circle-fill fs-1"></i>
                             <span class="fs-5">EXPIRADA</span>
                        </div>
                    <?php else: ?>
                         <div class="text-primary fw-bold d-flex flex-column align-items-center gap-2">
                             <i class="bi bi-shield-check-fill fs-1"></i>
                             <span class="fs-5">VÁLIDA</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="mb-2 d-flex justify-content-between">
                        <span>Criado em:</span>
                        <strong><?= (new DateTime($token['created_at']))->format('d/m/Y') ?></strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Expira em:</span>
                        <strong><?= (new DateTime($token['expires_at']))->format('d/m/Y') ?></strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Actions -->
        <?php if (!$isUsed && !$isExpired): ?>
            <div class="nd-card border-danger">
                <div class="nd-card-header bg-danger text-white d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <h5 class="nd-card-title mb-0 text-white">Zona de Revogação</h5>
                </div>
                <div class="nd-card-body">
                    <p class="small text-muted mb-3">
                        Revogar esta credencial impedirá o uso imediato para acesso ao sistema.
                    </p>
                    <form method="post" action="/admin/tokens/<?= (int)$token['id'] ?>/revoke"
                        onsubmit="return confirm('Tem certeza que deseja revogar esta credencial?');">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="nd-btn nd-btn-outline w-100 text-danger border-danger hover-danger-fill">
                            <i class="bi bi-trash me-2"></i> Revogar Acesso
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>