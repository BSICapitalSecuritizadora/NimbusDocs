<?php
/** @var array $announcements */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */

// Filtro simples PHP-side
$search = $_GET['search'] ?? '';

if ($search) {
    $announcements = array_filter($announcements, function($a) use ($search) {
        return stripos($a['title'], $search) !== false;
    });
}
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 nd-page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-megaphone-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Gestão de Comunicados</h1>
            <p class="text-muted mb-0 small">Publique avisos e notificações importantes para os usuários do portal.</p>
        </div>
    </div>
    <a href="/admin/announcements/new" class="nd-btn nd-btn-primary nd-btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Novo Comunicado
    </a>
</div>

<!-- Alerts -->
<?php if (!empty($success)): ?>
    <div class="nd-alert nd-alert-success mb-4" id="alertSuccess">
        <i class="bi bi-check-circle-fill text-success"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertSuccess').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="nd-alert nd-alert-danger mb-4" id="alertError">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="nd-alert-text"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <button type="button" class="nd-alert-close" onclick="document.getElementById('alertError').remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
<?php endif; ?>

<!-- Announcements List -->
<div class="nd-card">
    <div class="nd-card-header bg-white border-bottom p-3">
         <form class="row g-3 align-items-center" method="get" action="/admin/announcements">
            <div class="col-sm-8 col-md-10">
                <div class="nd-input-group">
                     <input type="text" name="search"
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
                        class="nd-input"
                        placeholder="Pesquisar por assunto do comunicado..."
                        style="padding-left: 2.5rem;">
                    <i class="bi bi-search nd-input-icon"></i>
                </div>
            </div>
            <div class="col-sm-4 col-md-2">
                <button class="nd-btn nd-btn-primary w-100" type="submit">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="nd-card-body p-0">
        <?php if (!$announcements): ?>
            <div class="text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-chat-square-quote text-muted" style="font-size: 1.5rem;"></i>
                </div>
                <p class="fw-medium text-dark mb-1">Nenhum comunicado encontrado</p>
                <p class="text-muted small mb-3">
                    <?= $search ? 'Não encontramos avisos com o termo pesquisado.' : 'O quadro de avisos está vazio.' ?>
                </p>
                <?php if (!$search): ?>
                <a href="/admin/announcements/new" class="btn btn-sm btn-link text-decoration-none p-0">
                    Criar o primeiro comunicado
                </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Assunto</th>
                            <th>Prioridade</th>
                            <th>Vigência</th>
                            <th>Situação</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $a): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark border font-monospace">#<?= (int)$a['id'] ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 300px;"><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <?php
                                    $level = $a['level'];
                                    $badgeClass = 'bg-secondary';
                                    $icon = '';
                                    $label = ucfirst($level);
                                    
                                    if ($level === 'info') {
                                        $badgeClass = 'bg-info text-white'; 
                                        $icon = 'bi-info-circle-fill';
                                        $label = 'Informativo';
                                    } elseif ($level === 'success') {
                                        $badgeClass = 'bg-success text-white';
                                        $icon = 'bi-check-circle-fill';
                                        $label = 'Positivo';
                                    } elseif ($level === 'warning') {
                                        $badgeClass = 'bg-warning text-dark';
                                        $icon = 'bi-exclamation-triangle-fill';
                                        $label = 'Atenção';
                                    } elseif ($level === 'danger') {
                                        $badgeClass = 'bg-danger text-white';
                                        $icon = 'bi-exclamation-octagon-fill';
                                        $label = 'Urgente';
                                    }
                                    ?>
                                    <span class="badge rounded-pill fw-normal <?= $badgeClass ?> d-inline-flex align-items-center gap-1">
                                        <?php if($icon): ?><i class="bi <?= $icon ?>"></i><?php endif; ?>
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small text-muted">
                                        <?php 
                                            // Helper to format roughly
                                            $fmt = fn($d) => $d ? date('d/m/Y H:i', strtotime($d)) : null;
                                            $start = $fmt($a['starts_at']);
                                            $end = $fmt($a['ends_at']);
                                        ?>
                                        <div><i class="bi bi-calendar-event me-1"></i> <strong class="text-dark">Início:</strong> <?= $start ?: 'Imediato' ?></div>
                                        <?php if ($end): ?>
                                            <div><i class="bi bi-hourglass-split me-1"></i> <strong class="text-dark">Fim:</strong>&nbsp;&nbsp;&nbsp;&nbsp;<?= $end ?></div>
                                        <?php else: ?>
                                            <div><i class="bi bi-infinity me-1"></i> Sem data final</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ((int)$a['is_active'] === 1): ?>
                                        <span class="nd-badge nd-badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="nd-badge nd-badge-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/admin/announcements/<?= (int)$a['id'] ?>/edit"
                                            class="nd-btn nd-btn-outline nd-btn-sm"
                                            title="Editar Comunicado">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button type="button" class="nd-btn nd-btn-outline nd-btn-sm text-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-id="<?= (int)$a['id'] ?>"
                                            data-title="<?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>"
                                            title="Remover Comunicado">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
             <div class="nd-card-footer p-3 border-top text-end text-muted small">
                Total de <strong><?= count($announcements) ?></strong> comunicados listados
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3 bg-danger-subtle text-danger">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trash-fill fs-5"></i>
                    <h5 class="modal-title fw-bold">Remover Comunicado</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="deleteForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <p class="text-dark mb-0">
                        Tem certeza que deseja excluir o comunicado <strong id="deleteTitle"></strong>?
                    </p>
                    <p class="text-muted small mt-2 mb-0">Ele deixará de ser exibido para todos os usuários.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="nd-btn nd-btn-outline nd-btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="nd-btn nd-btn-sm bg-danger text-white border-danger hover-danger-fill">
                        <i class="bi bi-trash me-1"></i> Excluir Definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteTitle = document.getElementById('deleteTitle');

deleteModal?.addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  const id = btn.dataset.id;
  const title = btn.dataset.title;
  
  deleteTitle.textContent = title;
  deleteForm.action = `/admin/announcements/${id}/delete`;
});
</script>

<style>
    @media (max-width: 575.98px) {
        .nd-page-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .nd-page-header > .d-flex {
            width: 100%;
        }
        .nd-page-header .nd-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>