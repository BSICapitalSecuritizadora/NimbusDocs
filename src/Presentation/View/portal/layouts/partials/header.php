<?php
/** 
 * Portal Header v2.0 - Premium Navigation
 * @var array|null $user 
 */
$user = $user ?? ($viewData['user'] ?? null);
$branding = $branding ?? ($viewData['branding'] ?? ($config['branding'] ?? []));
$appName  = $branding['app_name']        ?? 'BSI Capital';
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
                <!-- Logo Premium -->
                <div class="d-flex align-items-center gap-2">
                    <div class="portal-logo-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="d-flex flex-column lh-1">
                        <span class="portal-logo-title"><?= htmlspecialchars(explode(' ', $appName)[0]) ?></span>
                        <span class="portal-logo-subtitle">Securitizadora</span>
                    </div>
                </div>
            <?php endif; ?>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#portalNavbar" aria-controls="portalNavbar"
                aria-expanded="false" aria-label="Alternar navegação">
            <i class="bi bi-list text-white fs-4"></i>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="portalNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-2 align-items-center">
                <li class="nav-item">
                    <a class="nav-link portal-nav-link <?= $isActive('/portal') ? 'active' : '' ?>" href="/portal">
                        <i class="bi bi-house-door<?= $isActive('/portal') ? '-fill' : '' ?>"></i>
                        <span>Início</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link portal-nav-link <?= $isActive('/portal/submissions') && !$isNewSubmission ? 'active' : '' ?>" href="/portal/submissions">
                        <i class="bi bi-inbox<?= $isActive('/portal/submissions') && !$isNewSubmission ? '-fill' : '' ?>"></i>
                        <span>Meus Envios</span>
                    </a>
                </li>
                <li class="nav-item">
                     <a class="nav-link portal-nav-link <?= $isActive('/portal/documents/general') ? 'active' : '' ?>" href="/portal/documents/general">
                        <i class="bi bi-folder<?= $isActive('/portal/documents/general') ? '-fill' : '' ?>"></i>
                        <span>Documentos</span>
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <!-- New Submission Button -->
                <a class="nd-btn nd-btn-gold shadow-sm hover-scale text-decoration-none portal-new-btn" 
                   href="/portal/submissions/new">
                    <i class="bi bi-plus-lg"></i>
                    <span>Novo Envio</span>
                </a>

                <!-- User Menu -->
                <?php if ($user): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle portal-user-toggle" 
                           id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="portal-avatar">
                                <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                            </div>
                            <span class="d-none d-lg-block ms-2 text-white-50 small fw-medium">
                                <?= htmlspecialchars(explode(' ', $user['full_name'] ?? 'Usuário')[0]) ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0 rounded-3 overflow-hidden portal-dropdown" 
                            style="min-width: 280px;">
                            <!-- User Info Header -->
                            <li class="portal-dropdown-header">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="portal-avatar-lg">
                                        <?= strtoupper(substr($user['full_name'] ?? $user['email'], 0, 1)) ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <strong class="d-block text-dark text-truncate"><?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                                        <small class="text-muted d-block text-truncate"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></small>
                                    </div>
                                </div>
                            </li>
                            <!-- Menu Items -->
                            <li class="p-2">
                                <a class="dropdown-item rounded-2 py-2 mb-1 d-flex align-items-center gap-2" href="/portal/profile">
                                    <i class="bi bi-person-gear text-primary"></i> Meus Dados
                                </a>
                            </li>
                            <li><hr class="dropdown-divider m-0 opacity-25"></li>
                            <li class="p-2">
                                <a class="dropdown-item rounded-2 py-2 text-danger d-flex align-items-center gap-2" href="/portal/logout">
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

<style>
    /* Portal Header Premium Styles */
    .portal-navbar {
        background: linear-gradient(180deg, var(--nd-navy-900) 0%, var(--nd-navy-850, #0f2137) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding: 0.625rem 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Logo styles */
    .portal-logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--nd-gold-400) 0%, var(--nd-gold-600) 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--nd-navy-900);
        box-shadow: 0 0 15px rgba(212, 168, 75, 0.3);
    }
    
    .portal-logo-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        color: #ffffff;
        font-size: 1rem;
        letter-spacing: 0.3px;
    }
    
    .portal-logo-subtitle {
        font-size: 0.625rem;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    
    /* Navigation links */
    .portal-nav-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.65) !important;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.625rem 1.125rem !important;
        border-radius: 50px;
        transition: all 0.2s ease;
    }
    
    .portal-nav-link:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08);
    }
    
    .portal-nav-link.active {
        color: var(--nd-navy-900) !important;
        background: linear-gradient(135deg, var(--nd-gold-400) 0%, var(--nd-gold-500) 100%);
        font-weight: 600;
        box-shadow: 0 2px 12px rgba(212, 168, 75, 0.3);
    }
    
    /* New submission button */
    .portal-new-btn {
        padding: 0.5rem 1rem !important;
        font-size: 0.8125rem;
    }
    
    /* Avatar */
    .portal-avatar {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, var(--nd-navy-700) 0%, var(--nd-navy-800) 100%);
        border: 2px solid var(--nd-gold-500);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--nd-gold-400);
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .portal-user-toggle:hover .portal-avatar {
        box-shadow: 0 0 15px rgba(212, 168, 75, 0.3);
        transform: scale(1.05);
    }
    
    .portal-avatar-lg {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--nd-navy-700) 0%, var(--nd-navy-800) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--nd-gold-400);
        font-weight: 600;
        font-size: 1.125rem;
    }
    
    /* Dropdown */
    .portal-dropdown {
        animation: dropdownFade 0.2s ease;
    }
    
    .portal-dropdown-header {
        padding: 1rem;
        background: linear-gradient(180deg, var(--nd-surface-100) 0%, var(--nd-surface-50) 100%);
        border-bottom: 1px solid var(--nd-surface-200);
    }
    
    @keyframes dropdownFade {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Mobile adjustments */
    @media (max-width: 991.98px) {
        .portal-navbar .navbar-collapse {
            padding: 1rem 0;
        }
        
        .portal-nav-link {
            justify-content: center;
            margin: 0.25rem 0;
        }
        
        .portal-new-btn {
            width: 100%;
            justify-content: center;
            margin-top: 0.5rem;
        }
    }
</style>