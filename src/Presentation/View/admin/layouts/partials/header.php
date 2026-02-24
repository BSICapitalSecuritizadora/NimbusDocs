<?php
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']       ?? 'NimbusDocs';
$subtitle = $branding['app_subtitle']   ?? '';
$logoUrl  = $branding['admin_logo_url'] ?? '';
?>
<header class="navbar navbar-expand navbar-light bg-white border-bottom sticky-top">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="/admin/dashboard">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Logo"
                    style="height:28px" class="me-2">
            <?php endif; ?>
            <span>
                <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?>
                <?php if ($subtitle): ?>
                    <small class="d-block text-white-50"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></small>
                <?php endif; ?>
            </span>
        </a>

        <button class="btn btn-outline-secondary d-md-none" id="mobileMenuBtn">
            <i class="bi bi-list"></i>
        </button>

        <div class="ms-auto d-flex align-items-center">
            
            <!-- In-App Notifications Dropdown -->
            <div class="dropdown me-3" id="notificationDropdown">
                <button class="btn btn-link text-dark position-relative p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBellBtn">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationBadge" style="font-size: 0.6rem;">
                        0
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="width: 320px; max-height: 400px; overflow-y: auto;" id="notificationList">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Notificações</span>
                        <button class="btn btn-sm btn-link text-decoration-none py-0 d-none" id="markAllReadBtn" style="font-size: 0.8rem;">Marcar todas como lidas</button>
                    </div>
                    <div id="notificationItems">
                        <div class="text-center p-3 text-muted small">Carregando...</div>
                    </div>
                </div>
            </div>

            <?php $adminEmail = isset($admin) && is_array($admin) ? ($admin['email'] ?? '') : ''; ?>
            <?php if ($adminEmail !== ''): ?>
                <span class="me-3 text-muted small d-none d-md-inline"><?= htmlspecialchars((string)$adminEmail, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <a href="/admin/logout" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Sair</span>
            </a>
        </div>

    </div>
</header>

<script>
    document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
        document.getElementById('mobileSidebar').classList.toggle('show');
    });

    // --- In-App Notifications Polling Logic ---
    (function initNotifications() {
        const badge = document.getElementById('notificationBadge');
        const listContainer = document.getElementById('notificationItems');
        const markAllBtn = document.getElementById('markAllReadBtn');
        const csrfToken = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>';

        let unreadCount = 0;

        function fetchNotifications() {
            fetch('/admin/api/notifications')
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        updateUI(data.notifications, data.count);
                    }
                })
                .catch(err => console.error('Error fetching notifications:', err));
        }

        function updateUI(notifications, count) {
            unreadCount = count;
            
            // Update Badge
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
                markAllBtn.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
                markAllBtn.classList.add('d-none');
            }

            // Update List
            listContainer.innerHTML = '';
            if (notifications.length === 0) {
                listContainer.innerHTML = '<div class="text-center p-4 text-muted small"><i class="bi bi-check2-all fs-4 d-block mb-2"></i>Nenhuma notificação nova</div>';
                return;
            }

            notifications.forEach(notif => {
                const item = document.createElement('div');
                item.className = 'dropdown-item p-3 border-bottom text-wrap';
                item.style.cursor = 'pointer';
                
                let icon = 'bi-info-circle text-primary';
                if (notif.type === 'NEW_SUBMISSION') icon = 'bi-file-earmark-plus text-success';
                if (notif.type === 'SYSTEM_ALERT') icon = 'bi-exclamation-triangle text-warning';

                let timeStr = new Date(notif.created_at).toLocaleString('pt-BR', {day: 'numeric', month: 'short', hour:'2-digit', minute:'2-digit'});

                item.innerHTML = `
                    <div class="d-flex align-items-start">
                        <i class="bi ${icon} fs-4 me-3 mt-1"></i>
                        <div>
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">${escapeHtml(notif.title)}</strong>
                            <span class="d-block text-muted small mb-1">${escapeHtml(notif.message || '')}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">${timeStr}</small>
                        </div>
                    </div>
                `;

                item.addEventListener('click', () => {
                    markAsRead(notif.id, notif.link);
                });

                listContainer.appendChild(item);
            });
        }

        function markAsRead(id, link) {
            fetch(`/admin/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${encodeURIComponent(csrfToken)}`
            }).then(() => {
                if (link) {
                    window.location.href = link;
                } else {
                    fetchNotifications(); // Refresh list silently
                }
            });
        }

        if (markAllBtn) {
            markAllBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Keep dropdown open
                fetch('/admin/api/notifications/read-all', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `csrf_token=${encodeURIComponent(csrfToken)}`
                }).then(() => fetchNotifications());
            });
        }

        function escapeHtml(unsafe) {
            return (unsafe||'').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        // Initial fetch
        fetchNotifications();

        // Poll every 60 seconds
        setInterval(fetchNotifications, 60000);
    })();
</script>