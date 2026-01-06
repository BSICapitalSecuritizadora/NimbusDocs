<?php

/** @var array $announcements */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-megaphone-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Comunicados do Portal</h1>
            <p class="text-muted mb-0 small">Mensagens institucionais exibidas aos usuários do portal</p>
        </div>
    </div>
    <a href="/admin/announcements/new" class="nd-btn nd-btn-gold nd-btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Novo comunicado
    </a>
</div>

<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-3">
        <i class="bi bi-check-circle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="nd-alert nd-alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-list-ul" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Lista de Comunicados</h5>
    </div>
    <div class="nd-card-body p-0">
        <?php if (!$announcements): ?>
            <div class="text-center py-5">
                <i class="bi bi-chat-square-quote text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-2">Nenhum comunicado cadastrado.</p>
                <a href="/admin/announcements/new" class="btn btn-link text-decoration-none p-0">
                    Criar o primeiro comunicado
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Título</th>
                            <th>Nível</th>
                            <th>Período</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $a): ?>
                            <tr>
                                <td><span class="text-muted small">#<?= (int)$a['id'] ?></span></td>
                                <td>
                                    <div class="fw-medium text-dark"><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <?php
                                    $level = $a['level'];
                                    // Mapping levels to our palette roughly (or bootstrap classes if nd-badge not fully distinctive per generic level)
                                    $badgeClass = 'nd-badge-secondary';
                                    $icon = '';
                                    
                                    if ($level === 'info') {
                                        $badgeClass = 'bg-info text-white'; // Custom override or use nd-badge classes if available
                                        $icon = 'bi-info-circle';
                                    } elseif ($level === 'success') {
                                        $badgeClass = 'nd-badge-success';
                                        $icon = 'bi-check-circle';
                                    } elseif ($level === 'warning') {
                                        $badgeClass = 'bg-warning text-dark';
                                        $icon = 'bi-exclamation-triangle';
                                    } elseif ($level === 'danger') {
                                        $badgeClass = 'nd-badge-danger';
                                        $icon = 'bi-exclamation-octagon';
                                    }
                                    
                                    // For simplicity and premium look, let's use standard bootstrap colors inside our badge shape if nd-badges are limited
                                    // Actually we have nd-badge-success/danger/secondary. Let's stick to bootstrap standard for warning/info to ensure meaning.
                                    ?>
                                    <span class="badge rounded-pill fw-normal <?= $badgeClass ?>" style="font-size: 0.75rem;">
                                        <?php if($icon): ?><i class="bi <?= $icon ?> me-1"></i><?php endif; ?>
                                        <?= ucfirst(htmlspecialchars($level, ENT_QUOTES, 'UTF-8')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small text-muted">
                                        <?php 
                                            // Helper to format roughly
                                            $fmt = fn($d) => $d ? date('d/m/Y H:i', strtotime($d)) : null;
                                            $start = $fmt($a['starts_at']);
                                            $end = $fmt($a['ends_at']);
                                        ?>
                                        <span><i class="bi bi-calendar-event me-1"></i> <?= $start ?: 'Imediato' ?></span>
                                        <?php if ($end): ?>
                                            <span><i class="bi bi-arrow-down-right me-1 ms-1"></i> Até <?= $end ?></span>
                                        <?php else: ?>
                                            <span><i class="bi bi-infinite me-1 ms-1"></i> Indefinido</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ((int)$a['is_active'] === 1): ?>
                                        <span class="nd-badge nd-badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="nd-badge nd-badge-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="/admin/announcements/<?= (int)$a['id'] ?>/edit"
                                            class="nd-btn nd-btn-outline nd-btn-sm"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form method="post"
                                            action="/admin/announcements/<?= (int)$a['id'] ?>/delete"
                                            class="d-inline"
                                            onsubmit="return confirm('Remover este comunicado?');">
                                            <input type="hidden" name="_token"
                                                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>