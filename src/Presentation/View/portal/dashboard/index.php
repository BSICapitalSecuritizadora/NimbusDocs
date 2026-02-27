<?php
/**
 * Portal Dashboard v2.0 - Premium Client Experience
 * 
 * @var array $user
 * @var array $submissions
 * @var array $stats
 * @var array $announcements
 */
$announcements = $announcements ?? [];
$firstName = explode(' ', $user['full_name'] ?? $user['email'])[0];
?>

<div class="container-xxl">
    <!-- Welcome Banner -->
    <div class="nd-welcome-banner">
        <div class="nd-welcome-content">
            <div class="nd-welcome-greeting">
                <span class="nd-welcome-wave">👋</span>
                <span>Olá, <strong><?= htmlspecialchars($firstName) ?></strong>!</span>
            </div>
            <p class="nd-welcome-message">Bem-vindo ao seu portal exclusivo de solicitações e documentos.</p>
        </div>
        <div class="nd-welcome-cta">
            <a href="/portal/submissions/new" class="nd-btn nd-btn-gold shadow-lg">
                <i class="bi bi-plus-lg"></i>
                <span>Nova Solicitação</span>
            </a>
        </div>
    </div>

    <!-- Announcements (if any) -->
    <?php if (!empty($announcements)): ?>
        <div class="nd-announcements mb-4">
            <?php foreach ($announcements as $ann): ?>
                <?php
                $level = $ann['level'] ?? 'info';
                $config = match(strtolower($level)) {
                    'danger', 'urgente' => ['class' => 'danger', 'icon' => 'bi-exclamation-octagon-fill', 'label' => 'Urgente', 'color' => '#dc2626', 'bg' => '#fef2f2'],
                    'warning', 'atenção', 'atencao' => ['class' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'label' => 'Atenção', 'color' => '#d97706', 'bg' => '#fffbeb'],
                    'success', 'positivo' => ['class' => 'success', 'icon' => 'bi-check-circle-fill', 'label' => 'Positivo', 'color' => '#059669', 'bg' => '#ecfdf5'],
                    default => ['class' => 'info', 'icon' => 'bi-info-circle-fill', 'label' => 'Informativo', 'color' => '#0284c7', 'bg' => '#f0f9ff'],
                };
                ?>
                <div class="nd-announcement-card" id="ann-<?= $ann['id'] ?>" style="border-left: 4px solid <?= $config['color'] ?>;">
                    <div class="nd-announcement-icon" style="background: <?= $config['bg'] ?>; color: <?= $config['color'] ?>;">
                        <i class="bi <?= $config['icon'] ?>"></i>
                    </div>
                    <div class="nd-announcement-content" style="flex: 1;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge" style="background: <?= $config['color'] ?>; font-weight: 600; font-size: 0.65rem; padding: 0.35em 0.65em; border-radius: 4px;"><?= mb_strtoupper($config['label']) ?></span>
                            <h6 class="nd-announcement-title mb-0"><?= htmlspecialchars($ann['title'] ?? 'Aviso', ENT_QUOTES) ?></h6>
                        </div>
                        <p class="nd-announcement-text mt-1"><?= htmlspecialchars($ann['body'] ?? '', ENT_QUOTES) ?></p>
                    </div>
                    <div class="d-flex flex-column align-items-end ms-3">
                        <button type="button" class="btn-close mb-auto" aria-label="Close" onclick="dismissAnnouncement(<?= $ann['id'] ?>)" style="font-size: 0.75rem; opacity: 0.6;" title="Fechar aviso"></button>
                        <small class="nd-announcement-date mt-3">
                            <?= date('d/m/Y', strtotime($ann['created_at'] ?? 'now')) ?>
                        </small>
                    </div>
                </div>
                <script>
                    if (localStorage.getItem('nd_ann_dismissed_' + <?= $ann['id'] ?>)) {
                        document.getElementById('ann-<?= $ann['id'] ?>').style.display = 'none';
                    }
                </script>
            <?php endforeach; ?>
        </div>
        <script>
            function dismissAnnouncement(id) {
                localStorage.setItem('nd_ann_dismissed_' + id, 'true');
                const el = document.getElementById('ann-' + id);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => el.style.display = 'none', 300);
                }
            }
        </script>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="nd-stat-card nd-stat-card-primary">
                <div class="nd-stat-icon">
                    <i class="bi bi-inbox-fill"></i>
                </div>
                <div class="nd-stat-info">
                    <div class="nd-stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
                    <div class="nd-stat-label">Envios Realizados</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nd-stat-card nd-stat-card-gold">
                <div class="nd-stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="nd-stat-info">
                    <div class="nd-stat-value"><?= number_format($stats['pending'] ?? 0) ?></div>
                    <div class="nd-stat-label">Aguardando Análise</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nd-stat-card nd-stat-card-success">
                <div class="nd-stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="nd-stat-info">
                    <div class="nd-stat-value"><?= number_format($stats['approved'] ?? 0) ?></div>
                    <div class="nd-stat-label">Solicitações Aprovadas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center justify-content-between">
            <h5 class="nd-card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Suas Solicitações Recentes
            </h5>
            <a href="/portal/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
                Ver Todas
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
        <div class="nd-card-body p-0">
            <?php if (empty($submissions)): ?>
                <div class="nd-empty-state">
                    <div class="nd-empty-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h6>Nenhuma solicitação encontrada</h6>
                    <p>Você ainda não realizou nenhum envio. Clique no botão acima para criar sua primeira solicitação.</p>
                    <a href="/portal/submissions/new" class="nd-btn nd-btn-primary">
                        <i class="bi bi-plus-lg"></i>
                        Criar Primeira Solicitação
                    </a>
                </div>
            <?php else: ?>
                <div class="nd-table-wrapper" style="border: none; border-radius: 0;">
                    <table class="nd-table">
                        <thead>
                            <tr>
                                <th>Referência</th>
                                <th>Data de Envio</th>
                                <th>Situação</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($submissions, 0, 5) as $s): ?>
                                <?php
                                $statusConfig = match($s['status'] ?? '') {
                                    'PENDING'       => ['label' => 'Pendente', 'class' => 'warning', 'icon' => 'bi-clock'],
                                    'UNDER_REVIEW'  => ['label' => 'Em Análise', 'class' => 'info', 'icon' => 'bi-search'],
                                    'APPROVED'      => ['label' => 'Aprovada', 'class' => 'success', 'icon' => 'bi-check-circle'],
                                    'COMPLETED'     => ['label' => 'Concluída', 'class' => 'success', 'icon' => 'bi-check-all'],
                                    'REJECTED'      => ['label' => 'Rejeitada', 'class' => 'danger', 'icon' => 'bi-x-circle'],
                                    default         => ['label' => $s['status'] ?? '-', 'class' => 'neutral', 'icon' => 'bi-dash']
                                };
                                ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark">#<?= htmlspecialchars($s['id'] ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($s['submitted_at'] ?? 'now')) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="nd-badge nd-badge-<?= $statusConfig['class'] ?>">
                                            <i class="bi <?= $statusConfig['icon'] ?>"></i>
                                            <?= $statusConfig['label'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/portal/submissions/<?= $s['id'] ?>" class="nd-btn nd-btn-ghost nd-btn-sm" title="Ver detalhes">
                                            Detalhes
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Portal Dashboard Styles -->
<style>
    /* Welcome Banner */
    .nd-welcome-banner {
        background: linear-gradient(135deg, var(--nd-navy-800) 0%, var(--nd-navy-900) 100%);
        border-radius: var(--nd-radius-2xl);
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        box-shadow: var(--nd-shadow-lg);
        position: relative;
        overflow: hidden;
    }
    
    .nd-welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(212, 168, 75, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .nd-welcome-content {
        position: relative;
        z-index: 1;
    }
    
    .nd-welcome-greeting {
        font-family: var(--nd-font-heading);
        font-size: 1.5rem;
        color: #ffffff;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .nd-welcome-wave {
        font-size: 1.75rem;
        animation: wave 2s ease-in-out infinite;
    }
    
    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        75% { transform: rotate(-10deg); }
    }
    
    .nd-welcome-message {
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
        font-size: 0.9375rem;
    }
    
    .nd-welcome-cta {
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }
    
    .nd-welcome-cta .nd-btn {
        padding: 0.875rem 1.5rem;
        font-size: 0.9375rem;
    }
    
    /* Announcements */
    .nd-announcement-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: var(--nd-white);
        border: 1px solid var(--nd-surface-200);
        border-radius: var(--nd-radius-lg);
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        transition: var(--nd-transition);
    }
    
    .nd-announcement-card:hover {
        box-shadow: var(--nd-shadow-sm);
        border-color: var(--nd-gold-300);
    }
    
    .nd-announcement-icon {
        width: 40px;
        height: 40px;
        background: var(--nd-gold-100);
        color: var(--nd-gold-700);
        border-radius: var(--nd-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .nd-announcement-content {
        flex: 1;
    }
    
    .nd-announcement-title {
        font-weight: 600;
        color: var(--nd-navy-800);
        margin: 0 0 0.25rem;
        font-size: 0.9375rem;
    }
    
    .nd-announcement-text {
        color: var(--nd-gray-600);
        font-size: 0.8125rem;
        margin: 0;
        line-height: 1.5;
    }
    
    .nd-announcement-date {
        color: var(--nd-gray-400);
        font-size: 0.75rem;
        white-space: nowrap;
    }
    
    /* Stat Cards */
    .nd-stat-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        background: var(--nd-white);
        border: 1px solid var(--nd-surface-200);
        border-radius: var(--nd-radius-xl);
        padding: 1.5rem;
        transition: var(--nd-transition);
    }
    
    .nd-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--nd-shadow-md);
    }
    
    .nd-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--nd-radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .nd-stat-card-primary .nd-stat-icon {
        background: var(--nd-navy-100);
        color: var(--nd-navy-700);
    }
    
    .nd-stat-card-gold .nd-stat-icon {
        background: var(--nd-gold-100);
        color: var(--nd-gold-700);
    }
    
    .nd-stat-card-success .nd-stat-icon {
        background: var(--nd-success-light);
        color: var(--nd-success-dark);
    }
    
    .nd-stat-value {
        font-family: var(--nd-font-heading);
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--nd-navy-800);
        line-height: 1.1;
    }
    
    .nd-stat-label {
        color: var(--nd-gray-500);
        font-size: 0.8125rem;
        margin-top: 0.125rem;
    }
    
    /* Empty State */
    .nd-empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }
    
    .nd-empty-state .nd-empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: var(--nd-surface-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nd-empty-state .nd-empty-icon i {
        font-size: 2rem;
        color: var(--nd-gray-400);
    }
    
    .nd-empty-state h6 {
        color: var(--nd-navy-800);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .nd-empty-state p {
        color: var(--nd-gray-500);
        font-size: 0.875rem;
        max-width: 360px;
        margin: 0 auto 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .nd-welcome-banner {
            flex-direction: column;
            text-align: center;
            padding: 2rem 1.5rem;
        }
        
        .nd-welcome-cta .nd-btn {
            width: 100%;
        }
        
        .nd-stat-card {
            padding: 1.25rem;
        }
        
        .nd-stat-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
        }
        
        .nd-stat-value {
            font-size: 1.5rem;
        }
    }
</style>
