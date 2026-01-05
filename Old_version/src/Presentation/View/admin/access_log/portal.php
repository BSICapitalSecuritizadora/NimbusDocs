<?php

/** @var array $filters */
/** @var array $logs */

?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Log de acessos do portal</h1>
        <p class="text-muted small mb-0">
            Registro de logins, visualizações de submissões e downloads feitos pelos usuários finais.
        </p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-3">
                <label class="form-label small mb-1">E-mail do usuário</label>
                <input type="text" name="email" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Ação</label>
                <input type="text" name="action" class="form-control form-control-sm"
                    placeholder="LOGIN, VIEW..."
                    value="<?= htmlspecialchars($filters['action'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Tipo recurso</label>
                <input type="text" name="resource_type" class="form-control form-control-sm"
                    placeholder="submission, document..."
                    value="<?= htmlspecialchars($filters['resource_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">De</label>
                <input type="date" name="from_date" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Até</label>
                <input type="date" name="to_date" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!$logs): ?>
            <p class="text-muted small mb-0">Nenhum acesso encontrado para os filtros informados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Recurso</th>
                            <th>IP</th>
                            <th>User-Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="small text-muted">
                                    <?= htmlspecialchars($log['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="small">
                                    <?= htmlspecialchars($log['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                                    <span class="text-muted">
                                        <?= htmlspecialchars($log['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="small">
                                    <code><?= htmlspecialchars($log['action'] ?? '', ENT_QUOTES, 'UTF-8') ?></code>
                                </td>
                                <td class="small">
                                    <?= htmlspecialchars($log['resource_type'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($log['resource_id'])): ?>
                                        (#<?= (int)$log['resource_id'] ?>)
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="small text-muted">
                                    <?= htmlspecialchars($log['user_agent'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-2 mb-0">
                Máx. 200 registros por consulta. Refine os filtros para um intervalo menor, se necessário.
            </p>
        <?php endif; ?>
    </div>
</div>