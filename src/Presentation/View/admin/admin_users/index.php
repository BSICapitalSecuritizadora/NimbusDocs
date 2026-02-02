<?php

/** @var array $pagination */
/** @var string $csrfToken */
/** @var array $flash */

$items   = $pagination['items'] ?? [];
$page    = $pagination['page'] ?? 1;
$pages   = $pagination['pages'] ?? 1;
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-lock-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Gestão de Administradores</h1>
            <p class="text-muted mb-0 small">Controle de credenciais e níveis de acesso ao painel administrativo.</p>
        </div>
    </div>
    <a href="/admin/users/create" class="nd-btn nd-btn-gold nd-btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Novo Administrador
    </a>
</div>

<!-- Alerts -->
<?php if (!empty($flash['success'])): ?>
    <div class="nd-alert nd-alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
    <div class="nd-alert nd-alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<!-- Users List Card -->
<div class="nd-card">
    <div class="nd-card-body p-0">
        <div class="table-responsive">
            <table class="nd-table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Nível de Acesso</th>
                        <th>Status</th>
                        <th>Último Acesso</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-people text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">Nenhum administrador cadastrado no sistema.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nd-avatar" style="width: 36px; height: 36px; background: var(--nd-gray-100); color: var(--nd-gray-600); font-size: 0.8rem;">
                                            <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-medium text-dark"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'SUPER_ADMIN'): ?>
                                        <span class="d-inline-flex align-items-center gap-1 text-primary small fw-semibold">
                                            <i class="bi bi-shield-shaded"></i> Master
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Administrador</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match ($user['status'] ?? '') {
                                        'ACTIVE' => 'success',
                                        'BLOCKED' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusLabel = match ($user['status'] ?? '') {
                                        'ACTIVE' => 'Ativo',
                                        'BLOCKED' => 'Bloqueado',
                                        'INACTIVE' => 'Inativo',
                                        default => 'Não Identificado'
                                    };
                                    ?>
                                    <span class="nd-badge nd-badge-<?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['last_login_at']): ?>
                                        <div class="small text-dark">
                                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                                            <?= (new DateTime($user['last_login_at']))->format('d/m/Y H:i') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Nunca acessou</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/admin/users/<?= (int)$user['id'] ?>/edit" class="nd-btn nd-btn-outline nd-btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <?php if ($user['status'] === 'ACTIVE'): ?>
                                            <form method="post" action="/admin/users/<?= (int)$user['id'] ?>/deactivate"
                                                class="d-inline"
                                                onsubmit="return confirm('Deseja desativar este administrador?');">
                                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="nd-btn nd-btn-outline nd-btn-sm text-danger" title="Desativar">
                                                    <i class="bi bi-person-x-fill"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($pages > 1): ?>
            <div class="nd-card-footer p-3 border-top">
                <nav>
                    <ul class="pagination pagination-sm justify-content-end mb-0">
                        <?php for ($p = 1; $p <= $pages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="/admin/users?page=<?= $p ?>">
                                    <?= $p ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    @media (max-width: 575.98px) {
        .nd-page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .nd-page-header > .d-flex {
            width: 100%;
        }
        .nd-page-header .nd-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>