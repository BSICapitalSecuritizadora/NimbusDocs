<?php

/** @var array|null $user */
$user = $user ?? ($viewData['user'] ?? null);
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']        ?? 'NimbusDocs';
$logoUrl  = $branding['portal_logo_url'] ?? '';
?>
<nav class="navbar navbar-expand-lg portal-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/portal">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Logo"
                    style="height:26px" class="me-2">
            <?php endif; ?>
            <span><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#portalNavbar" aria-controls="portalNavbar"
            aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="portalNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link<?= ($_SERVER['REQUEST_URI'] === '/portal') ? ' active' : '' ?>"
                        href="/portal">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/portal/submissions') ? ' active' : '' ?>"
                        href="/portal/submissions">Meus envios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= ($_SERVER['REQUEST_URI'] === '/portal/submissions/new') ? ' active' : '' ?>"
                        href="/portal/submissions/new">Nova submissão</a>
                </li>
            </ul>

            <?php if ($user): ?>
                <span class="navbar-text me-3 small">
                    <?= htmlspecialchars($user['full_name'] ?? $user['email'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <!-- Se tiver logout específico do portal -->
                <a href="/portal/logout" class="btn btn-outline-light btn-sm">
                    Sair
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>