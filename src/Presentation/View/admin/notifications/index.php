<?php
/**
 * View showing all in-app notifications
 * @var array $notifications
 * @var int $page
 * @var bool $hasMore
 */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
  <div class="d-flex align-items-center gap-3">
    <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
        <i class="bi bi-bell-fill text-white"></i>
    </div>
    <div>
        <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Todas as Notificações</h1>
        <p class="text-muted mb-0 small">Histórico de alertas e avisos do sistema</p>
    </div>
  </div>
  <div class="dropdown">
    <button class="nd-btn nd-btn-outline" type="button" id="markAllReadPageBtn">
      <i class="bi bi-check2-all me-2"></i> Marcar todas como lidas
    </button>
  </div>
</div>

<div class="nd-card">
    <div class="nd-card-body p-0">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash text-muted mb-3 d-block" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="text-muted">Nenhuma notificação</h5>
                <p class="text-muted small">Você não possui notificações no momento.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush" id="notificationPageList">
                <?php foreach ($notifications as $notif): ?>
                    <?php
                        $isRead = !empty($notif['is_read']);
                        $icon = $notif['type'] === 'system' ? 'bi-info-circle' : 'bi-bell';
                        $iconColor = $notif['type'] === 'system' ? 'text-primary' : 'text-warning';
                        $bgColor = $isRead ? 'bg-transparent' : 'bg-light';
                        
                        $dateStr = '';
                        if (!empty($notif['created_at'])) {
                            try {
                                $d = new DateTime($notif['created_at']);
                                $dateStr = $d->format('d/m/Y H:i');
                            } catch (Exception $e) {
                                $dateStr = $notif['created_at'];
                            }
                        }
                    ?>
                    <a href="<?= htmlspecialchars($notif['link_url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" 
                       class="list-group-item list-group-item-action p-4 <?= $bgColor ?> border-bottom d-flex gap-3 align-items-start"
                       onclick="markNotificationRead(<?= (int)$notif['id'] ?>)">
                        
                        <div class="mt-1">
                            <i class="bi <?= $icon ?> <?= $iconColor ?> fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold <?= $isRead ? 'text-secondary' : 'text-dark' ?>">
                                    <?= htmlspecialchars($notif['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h6>
                                <small class="text-muted"><?= $dateStr ?></small>
                            </div>
                            <p class="mb-0 text-secondary" style="font-size: 0.9rem;">
                                <?= htmlspecialchars($notif['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <?php if (!$isRead): ?>
                            <div class="mt-2">
                                <span class="badge bg-primary rounded-pill p-1"> </span>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($page > 1 || $hasMore): ?>
        <div class="nd-card-footer p-3 border-top d-flex justify-content-between align-items-center">
            <?php if ($page > 1): ?>
                <a href="/admin/notifications?page=<?= $page - 1 ?>" class="nd-btn nd-btn-outline nd-btn-sm">
                    <i class="bi bi-chevron-left me-1"></i> Anterior
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>
            
            <span class="text-muted small">Página <?= $page ?></span>
            
            <?php if ($hasMore): ?>
                <a href="/admin/notifications?page=<?= $page + 1 ?>" class="nd-btn nd-btn-outline nd-btn-sm">
                    Próxima <i class="bi bi-chevron-right ms-1"></i>
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllBtn = document.getElementById('markAllReadPageBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async function() {
            try {
                const response = await fetch('/admin/api/notifications/read-all', { method: 'POST' });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error('Failed to mark all as read', e);
            }
        });
    }
});
</script>
