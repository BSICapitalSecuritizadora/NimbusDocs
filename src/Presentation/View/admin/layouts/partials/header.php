<header class="navbar navbar-expand navbar-light bg-white border-bottom sticky-top">
    <div class="container-fluid">

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