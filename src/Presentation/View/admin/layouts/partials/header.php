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
            <?php $adminEmail = isset($admin) && is_array($admin) ? ($admin['email'] ?? '') : ''; ?>
            <?php if ($adminEmail !== ''): ?>
                <span class="me-3 text-muted small"><?= htmlspecialchars((string)$adminEmail, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>

            <a href="/admin/logout" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Sair
            </a>
        </div>

    </div>
</header>

<script>
    document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
        document.getElementById('mobileSidebar').classList.toggle('show');
    });
</script>