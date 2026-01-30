<?php
/** @var array|null $user */
$user = $user ?? ($viewData['user'] ?? null);
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']        ?? 'NimbusDocs';
$logoUrl  = $branding['portal_logo_url'] ?? '';

// Helper para verificar rota ativa
$isActive = fn($path) => 
    $path === '/portal' ? $_SERVER['REQUEST_URI'] === '/portal' : str_starts_with($_SERVER['REQUEST_URI'], $path);

$isNewSubmission = $_SERVER['REQUEST_URI'] === '/portal/submissions/new';
?>
<nav class="navbar navbar-expand-lg portal-navbar sticky-top">
    <div class="container-xxl">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-3" href="/portal">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt="Logo"
                     class="navbar-logo"
                     style="height: 40px; width: auto;">
            <?php else: ?>
                <!-- Logo Fallback Premium -->
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-gradient-gold text-navy rounded-2 d-flex align-items-center justify-content-center fw-bold" 
                         style="width: 36px; height: 36px; background: var(--nd-gold-400);">
                        <i class="bi bi-shield-check" style="font-size: 1.2rem; color: var(--nd-navy-900);"></i>
                    </div>
                    <div class="d-flex flex-column lh-1">
                        <span class="fw-bold text-white text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.5px;">BSI Capital</span>
                        <span class="text-white-50 x-small text-uppercase" style="font-size: 0.65rem; letter-spacing: 2px;">Securitizadora</span>
                    </div>
                </div>
            <?php endif; ?>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#portalNavbar" aria-controls="portalNavbar"
                aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="portalNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-2 align-items-center">
                <li class="nav-item">
                    <a class="nav-link portal-nav-link <?= $isActive('/portal') ? 'active' : '' ?>" href="/portal">
                        <i class="bi bi-house-door<?= $isActive('/portal') ? '-fill' : '' ?>"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link portal-nav-link <?= $isActive('/portal/submissions') && !$isNewSubmission ? 'active' : '' ?>" href="/portal/submissions">
                        <i class="bi bi-inbox<?= $isActive('/portal/submissions') && !$isNewSubmission ? '-fill' : '' ?>"></i> Meus Envios
                    </a>
                </li>
                <li class="nav-item">
                     <a class="nav-link portal-nav-link <?= $isActive('/portal/documents/general') ? 'active' : '' ?>" href="/portal/documents/general">
                        <i class="bi bi-folder<?= $isActive('/portal/documents/general') ? '-fill' : '' ?>"></i> Documentos
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a class="nd-btn nd-btn-accent shadow-sm hover-scale text-decoration-none" 
                   href="/portal/submissions/new">
                    <i class="bi bi-plus-lg"></i> Novo Envio
                </a>

                <!-- User Menu -->
                <?php if ($user): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle p-1 rounded-pill hover-bg-light-10" 
                           id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="nd-avatar" style="width: 38px; height: 38px; border: 2px solid var(--nd-gold-500); background: var(--nd-navy-800);">
                                <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0 rounded-3 overflow-hidden" 
                            style="min-width: 260px;">
                            <li class="p-3 bg-light border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="nd-avatar" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <strong class="d-block text-dark text-truncate" style="font-size: 0.95rem;"><?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                        <small class="text-muted d-block text-truncate small"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                    </div>
                                </div>
                            </li>
                            <li class="p-2">
                                <a class="dropdown-item rounded-2 py-2 mb-1 d-flex align-items-center gap-2" href="/portal/profile">
                                    <i class="bi bi-person-gear text-primary"></i> Meus Dados
                                </a>
                            </li>
                            <li><hr class="dropdown-divider m-0 opacity-25"></li>
                            <li class="p-2">
                                <a class="dropdown-item rounded-2 py-2 text-danger d-flex align-items-center gap-2 hover-bg-danger-subtle" href="/portal/logout">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>