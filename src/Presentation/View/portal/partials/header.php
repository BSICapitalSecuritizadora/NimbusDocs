<?php

use App\Support\Session;

$portalUser = Session::get('portal_user');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/portal">NimbusDocs Portal</a>

        <div class="d-flex">
            <?php if ($portalUser): ?>
                <span class="navbar-text me-3 small">
                    Olá, <strong><?= htmlspecialchars($portalUser['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                </span>
                <a href="/portal/logout" class="btn btn-outline-light btn-sm">
                    Sair
                </a>
            <?php else: ?>
                <a href="/portal/login" class="btn btn-outline-light btn-sm">
                    Entrar
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>