<?php
/**
 * View para gerenciamento de Relatórios Agendados
 * 
 * @var array $schedules Lista de agendamentos Atuais
 */
?>

<div class="container-fluid px-0">
    <div class="nd-page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="nd-page-title">Relatórios Agendados</h1>
            <p class="nd-page-subtitle">Configure envios automáticos de relatórios por e-mail</p>
        </div>
        <div>
            <button class="nd-btn nd-btn-primary" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
                <i class="bi bi-plus-lg"></i> Novo Agendamento
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success d-flex align-items-center mb-4 border-0">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4 border-0">
            <i class="bi bi-x-circle-fill me-2"></i>
            <div><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="nd-card">
        <div class="nd-card-body p-0">
            <div class="table-responsive">
                <table class="table nd-table mb-0">
                    <thead>
                        <tr>
                            <th>Relatório</th>
                            <th>Frequência</th>
                            <th>Destinatários</th>
                            <th>Próximo Envio</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedules)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Nenhum relatório agendado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($schedules as $sched): ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium text-dark">
                                            <?php 
                                            // Simplification for MVP, only Submissions supported currently
                                            echo $sched['report_type'] === 'submissions' ? 'Relatório de Submissões' : htmlspecialchars($sched['report_type']);
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?php
                                            echo match($sched['frequency']) {
                                                'DAILY'   => 'Diário',
                                                'WEEKLY'  => 'Semanal',
                                                'MONTHLY' => 'Mensal',
                                                default   => $sched['frequency']
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td style="max-width: 200px;" class="text-truncate">
                                        <?php 
                                        $emails = json_decode($sched['recipient_emails'], true) ?: [];
                                        echo htmlspecialchars(implode(', ', $emails));
                                        ?>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($sched['next_run_at'])) ?>
                                        </div>
                                        <?php if ($sched['last_run_at']): ?>
                                        <div style="font-size: 0.7rem;" class="text-muted">
                                            Última: <?= date('d/m/Y H:i', strtotime($sched['last_run_at'])) ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($sched['is_active']): ?>
                                            <span class="nd-badge nd-badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="nd-badge nd-badge-neutral">Pausado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="nd-btn nd-btn-ghost nd-btn-sm" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <form method="POST" action="/admin/reports/schedules/<?= $sched['id'] ?>/toggle">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi <?= $sched['is_active'] ? 'bi-pause-circle text-warning' : 'bi-play-circle text-success' ?> me-2"></i>
                                                            <?= $sched['is_active'] ? 'Pausar Envio' : 'Retomar Envio' ?>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="/admin/reports/schedules/<?= $sched['id'] ?>/delete" onsubmit="return confirm('Excluir este agendamento definitivamente?');">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i> Excluir
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Agendamento -->
<div class="modal fade" id="newScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Novo Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/reports/schedules" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium">Tipo de Relatório</label>
                        <select name="report_type" class="form-select" required>
                            <option value="submissions">Relatório Geral de Submissões</option>
                            <!-- Future reports can be added here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium">Frequência</label>
                        <select name="frequency" class="form-select" required>
                            <option value="DAILY">Diário (Todos os dias)</option>
                            <option value="WEEKLY" selected>Semanal (Toda Segunda-feira)</option>
                            <option value="MONTHLY">Mensal (Dia 1 de cada mês)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium">E-mails Destinatários</label>
                        <input type="text" name="recipient_emails" class="form-control" placeholder="admin@empresa.com, joao@empresa.com" required>
                        <div class="form-text">Separe múltiplos e-mails por vírgula.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="nd-btn nd-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-primary">Criar Agendamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .nd-page-title {
        font-family: var(--nd-font-heading);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--nd-navy-800);
        margin: 0;
    }
    .nd-page-subtitle {
        font-size: 0.875rem;
        color: var(--nd-gray-500);
        margin: 0.25rem 0 0;
    }
</style>
