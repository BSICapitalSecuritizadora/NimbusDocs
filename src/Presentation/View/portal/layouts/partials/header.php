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
<nav class="navbar navbar-expand-lg portal-navbar py-3 sticky-top transition-all">
    <div class="container-xxl">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-3" href="/portal">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt="Logo"
                     class="navbar-logo"
                     style="height: 40px; width: auto;">
            <?php else: ?>
                <img src="https://media.licdn.com/dms/image/v2/D4D0BAQExaECDvucniw/company-logo_200_200/B4DZbwVBVeG0AI-/0/1747788764990/bsi_capital_securitizadora_s_a_logo?e=2147483647&v=beta&t=NwW3hFxem07njQLPtUFvIAOnOeq_tsRDcli7lc8drrI" 
                     alt="BSI Capital" 
                     class="shadow-sm rounded"
                     style="height: 40px; width: auto;">
                <div class="d-flex flex-column lh-1">
                    <span class="fw-bold text-white ls-1 text-uppercase" style="font-size: 0.95rem;">BSI Capital</span>
                    <span class="text-white-50 x-small text-uppercase ls-2" style="font-size: 0.65rem;">Securitizadora</span>
                </div>
            <?php endif; ?>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0 shadow-none text-white p-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#portalNavbar" aria-controls="portalNavbar"
                aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="portalNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-4 align-items-center">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?= $isActive('/portal') ? 'active' : '' ?>" href="/portal">
                        <i class="bi bi-house-door<?= $isActive('/portal') ? '-fill' : '' ?>"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?= $isActive('/portal/submissions') && !$isNewSubmission ? 'active' : '' ?>" href="/portal/submissions">
                        <i class="bi bi-inbox<?= $isActive('/portal/submissions') && !$isNewSubmission ? '-fill' : '' ?>"></i> Meus Envios
                    </a>
                </li>
                <li class="nav-item">
                     <a class="nav-link d-flex align-items-center gap-2 <?= $isActive('/portal/documents/general') ? 'active' : '' ?>" href="/portal/documents/general">
                        <i class="bi bi-folder<?= $isActive('/portal/documents/general') ? '-fill' : '' ?>"></i> Documentos
                    </a>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <div class="vr h-100 bg-white opacity-25 mx-2"></div>
                </li>
                <li class="nav-item">
                    <a class="btn btn-sm nd-btn-gold text-dark fw-bold d-flex align-items-center gap-2 px-3 shadow-sm hover-scale" 
                       href="/portal/submissions/new">
                        <i class="bi bi-plus-lg"></i> Novo Envio
                    </a>
                </li>
            </ul>

            <!-- User Menu -->
            <?php if ($user): ?>
                <div class="dropdown mt-3 mt-lg-0">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle user-menu-toggle px-3 py-1" 
                       id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar text-bg-gold rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm border border-2 border-white-10">
                            <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                        </div>
                        <div class="d-none d-lg-flex flex-column text-start me-1">
                            <span class="fw-bold fs-7 lh-1 text-white">
                                <?= htmlspecialchars(explode(' ', $user['full_name'] ?? $user['email'])[0], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0 rounded-4 overflow-hidden" aria-labelledby="dropdownUser1" style="min-width: 240px;">
                        <li class="p-3 bg-light border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                    <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                                </div>
                                <div class="overflow-hidden">
                                    <strong class="d-block text-dark text-truncate"><?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                    <small class="text-muted d-block text-truncate x-small"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                            </div>
                        </li>
                        <li class="p-2">
                            <a class="dropdown-item rounded-3 py-2 mb-1 d-flex align-items-center gap-2" href="/portal/profile">
                                <i class="bi bi-person-gear text-primary"></i> Meus Dados
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0 opacity-50"></li>
                        <li class="p-2">
                            <a class="dropdown-item rounded-3 py-2 text-danger d-flex align-items-center gap-2 hover-bg-danger-subtle" href="/portal/logout">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* Header CSS Overrides needed for specific tweaks */
.nav-link {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    transition: all 0.2s;
    color: rgba(255,255,255,0.7) !important;
}
.nav-link:hover, .nav-link.active {
    color: #fff !important;
    background: rgba(255,255,255,0.08);
}
.navbar-brand {
    font-size: 1.5rem; 
}
.fs-7 { font-size: 0.85rem; }
.hover-bg-danger-subtle:hover { background-color: var(--bs-danger-bg-subtle) !important; }
</style>