<?php

use App\Support\Session;

$admin = Session::get('admin');
?>
<header class="bg-dark text-white py-2">
    <div class="container d-flex align-items-center justify-content-between">
        <span class="fw-semibold">
            NimbusDocs <small class="text-secondary">Admin</small>
        </span>

        <div class="d-flex align-items-center gap-3 small">
            <?php if ($admin): ?>
                <span>
                    Logado como: <strong><?= htmlspecialchars($admin['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                </span>
                <a href="/admin/logout" class="link-light text-decoration-none">
                    Sair
                </a>
            <?php else: ?>
                <span class="text-secondary">
                    Não autenticado
                </span>
            <?php endif; ?>
        </div>
    </div>
</header>