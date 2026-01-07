<?php
/** @var array|null $user */
$user = $user ?? ($viewData['user'] ?? null);
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']        ?? 'NimbusDocs';
$logoUrl  = $branding['portal_logo_url'] ?? '';

// Helper para verificar rota ativa
$isActive = fn($path) => 
    $path === '/portal' ? $_SERVER['REQUEST_URI'] === '/portal' : str_starts_with($_SERVER['REQUEST_URI'], $path);
?>
<nav class="navbar navbar-expand-lg portal-navbar py-3 sticky-top">
    <div class="container-xxl">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/portal">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt="Logo"
                     class="navbar-logo">
            <?php else: ?>
                <div class="brand-icon">
                    <i class="bi bi-cloud-check-fill"></i>
                </div>
            <?php endif; ?>
            <span class="brand-text"><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#portalNavbar" aria-controls="portalNavbar"
                aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="portalNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/portal' ? 'active' : '' ?>" href="/portal">
                        <i class="bi bi-house-door-fill"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/portal/submissions') && $_SERVER['REQUEST_URI'] !== '/portal/submissions/new' ? 'active' : '' ?>" href="/portal/submissions">
                        <i class="bi bi-inbox-fill"></i> Meus envios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/portal/submissions/new' ? 'active' : '' ?>" href="/portal/submissions/new">
                        <i class="bi bi-plus-circle-fill"></i> Nova submissão
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/portal/documents/general" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/portal/documents/general') ? 'active' : '' ?>">
                        <i class="bi bi-folder-fill"></i> Documentos gerais
                    </a>
                </li>
            </ul>

            <?php if ($user): ?>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle user-menu-toggle" 
                       id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar text-bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                            <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                        </div>
                        <span class="d-none d-lg-inline fs-6 me-1">
                            <?= htmlspecialchars(explode(' ', $user['full_name'] ?? $user['email'])[0], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-3 text-small" aria-labelledby="dropdownUser1">
                        <li>
                            <h6 class="dropdown-header text-uppercase small fw-bold text-muted mb-2">
                                Conta
                            </h6>
                            <div class="px-3 pb-2 mb-2 border-bottom">
                                <strong class="d-block text-dark"><?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                <small class="text-muted d-block text-truncate"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></small>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2 mb-1" href="/portal/profile">
                                <i class="bi bi-person-gear me-2 text-secondary"></i> Meu Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2 text-danger" href="/portal/logout">
                                <i class="bi bi-box-arrow-right me-2"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>