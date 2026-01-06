<?php
/** @var array $filters */
/** @var array $logs */
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-shield-lock-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Log de Acessos do Portal</h1>
            <p class="text-muted mb-0 small">Registro de logins, visualizações e atividades dos usuários.</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="nd-card mb-4">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill" style="color: var(--nd-gold-500);"></i>
        <h5 class="nd-card-title mb-0">Filtros</h5>
    </div>
    <div class="nd-card-body">
        <form class="row g-3">
            <div class="col-md-3">
                <label class="nd-label">E-mail do usuário</label>
                <div class="nd-input-group">
                    <input type="text" name="email" class="nd-input"
                        placeholder="Ex: usuario@email.com"
                        value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-envelope nd-input-icon"></i>
                </div>
            </div>
            
            <div class="col-md-2">
                <label class="nd-label">Ação</label>
                <input type="text" name="action" class="nd-input"
                    placeholder="Ex: LOGIN"
                    value="<?= htmlspecialchars($filters['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="col-md-2">
                <label class="nd-label">Tipo recurso</label>
                <input type="text" name="resource_type" class="nd-input"
                    placeholder="Ex: submission"
                    value="<?= htmlspecialchars($filters['resource_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            
            <div class="col-md-2">
                <label class="nd-label">De</label>
                <div class="nd-input-group">
                    <input type="date" name="from_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            
            <div class="col-md-2">
                <label class="nd-label">Até</label>
                <div class="nd-input-group">
                    <input type="date" name="to_date" class="nd-input"
                        value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="nd-btn nd-btn-primary w-100" title="Filtrar">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Logs List -->
<div class="nd-card">
    <div class="nd-card-header d-flex align-items-center gap-2">
        <i class="bi bi-list-ul" style="color: var(--nd-navy-500);"></i>
        <h5 class="nd-card-title mb-0">Registros Encontrados</h5>
    </div>
    <div class="nd-card-body p-0">
        <?php if (!$logs): ?>
            <div class="text-center py-5">
                <i class="bi bi-search text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-0">Nenhum acesso encontrado para os filtros informados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Recurso</th>
                            <th>Detalhes Técnicos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <div class="small text-dark fw-medium">
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($log['created_at']))) ?>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= htmlspecialchars(date('H:i:s', strtotime($log['created_at']))) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-medium small">
                                            <?= htmlspecialchars($log['user_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="text-muted x-small">
                                            <?= htmlspecialchars($log['user_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="nd-badge nd-badge-secondary font-monospace">
                                        <?= htmlspecialchars($log['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <span class="text-muted text-uppercase x-small d-block">Tipo</span>
                                        <?= htmlspecialchars($log['resource_type'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        <?php if (!empty($log['resource_id'])): ?>
                                            <span class="text-muted ms-1">#<?= (int)$log['resource_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="small text-muted">
                                            <i class="bi bi-globe me-1"></i>
                                            <?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($log['user_agent'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-laptop me-1"></i>
                                            <?= htmlspecialchars($log['user_agent'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top bg-light">
                <p class="text-muted x-small mb-0 text-center">
                    <i class="bi bi-info-circle me-1"></i>
                    Mostrando os últimos 200 registros. Refine os filtros para buscar períodos específicos.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>