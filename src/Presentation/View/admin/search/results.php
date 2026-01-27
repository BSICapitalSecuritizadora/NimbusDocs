<?php
/**
 * Search Results View
 * @var string $query
 * @var array $results
 * @var array $branding
 */

$appName = $branding['app_name'] ?? 'NimbusDocs';
$submissions = $results['submissions'] ?? [];
$users = $results['users'] ?? [];
$documents = $results['documents'] ?? [];
$total = $results['total'] ?? 0;

$statusLabels = [
    'PENDING'      => ['label' => 'Pendente',   'class' => 'nd-badge-warning'],
    'UNDER_REVIEW' => ['label' => 'Em Análise', 'class' => 'nd-badge-info'],
    'COMPLETED'    => ['label' => 'Concluída',  'class' => 'nd-badge-success'],
    'REJECTED'     => ['label' => 'Rejeitada',  'class' => 'nd-badge-danger'],
];

// Fallback for missing classes if needed, or map directly to Bootstrap colors if nd-badge only has specific variants
// Assuming nd-badge-secondary, nd-badge-success, nd-badge-danger, nd-badge-warning exist.
// 'info' might need mapping to secondary or a custom class.

$userStatusLabels = [
    'INVITED'  => ['label' => 'Convidado', 'class' => 'nd-badge-secondary'],
    'ACTIVE'   => ['label' => 'Ativo',     'class' => 'nd-badge-success'],
    'INACTIVE' => ['label' => 'Inativo',   'class' => 'nd-badge-warning'],
    'BLOCKED'  => ['label' => 'Bloqueado', 'class' => 'nd-badge-danger'],
];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-search text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Resultados da Busca</h1>
            <p class="text-muted mb-0 small">
                <?php if ($total > 0): ?>
                    Encontrados <?= $total ?> registro(s) para "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php else: ?>
                    Nenhum resultado para "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php endif; ?>
            </p>
        </div>
    </div>
    <a href="/admin/dashboard" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
</div>

<?php if ($total === 0): ?>
    <div class="row">
        <div class="col-12">
            <div class="nd-card">
                <div class="nd-card-body text-center py-5">
                    <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="nd-card-title text-muted">Nenhum resultado encontrado</h5>
                    <p class="text-muted mb-0">Tente buscar por outros termos ou verifique a ortografia.</p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <!-- Submissions -->
        <?php if (!empty($submissions)): ?>
            <div class="col-12">
                <div class="nd-card h-100">
                    <div class="nd-card-header d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text text-primary"></i>
                        <h5 class="nd-card-title mb-0">Submissões (<?= count($submissions) ?>)</h5>
                    </div>
                    <div class="nd-card-body p-0">
                        <div class="table-responsive">
                            <table class="nd-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Título</th>
                                        <th>Usuário</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $sub): ?>
                                        <?php 
                                            $status = $statusLabels[$sub['status']] ?? ['label' => $sub['status'], 'class' => 'nd-badge-secondary'];
                                            // Handling 'info' mapping if strictly needed
                                            if ($sub['status'] === 'UNDER_REVIEW') $status['class'] = 'badge bg-info text-white'; 
                                        ?>
                                        <tr>
                                            <td>
                                                <code class="text-muted fw-bold"><?= htmlspecialchars($sub['reference_code']) ?></code>
                                            </td>
                                            <td>
                                                <div class="fw-medium text-dark"><?= htmlspecialchars($sub['title']) ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark small fw-medium"><?= htmlspecialchars($sub['user_name']) ?></span>
                                                    <span class="text-muted x-small"><?= htmlspecialchars($sub['user_email']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="<?= str_starts_with($status['class'], 'nd-badge') ? 'nd-badge ' . $status['class'] : $status['class'] . ' rounded-pill px-2 py-1 small' ?>">
                                                    <?= $status['label'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small">
                                                    <?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/submissions/<?= $sub['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Users -->
        <?php if (!empty($users)): ?>
            <div class="col-12">
                <div class="nd-card h-100">
                    <div class="nd-card-header d-flex align-items-center gap-2">
                        <i class="bi bi-people text-success"></i>
                        <h5 class="nd-card-title mb-0">Usuários do Portal (<?= count($users) ?>)</h5>
                    </div>
                    <div class="nd-card-body p-0">
                        <div class="table-responsive">
                            <table class="nd-table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Documento</th>
                                        <th>Status</th>
                                        <th>Cadastro</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <?php 
                                            $status = $userStatusLabels[$user['status']] ?? ['label' => $user['status'], 'class' => 'nd-badge-secondary'];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-medium text-dark"><?= htmlspecialchars($user['full_name']) ?></div>
                                            </td>
                                            <td><span class="text-muted small"><?= htmlspecialchars($user['email'] ?? '-') ?></span></td>
                                            <td><span class="text-muted small"><?= htmlspecialchars($user['document_number'] ?? '-') ?></span></td>
                                            <td>
                                                <span class="nd-badge <?= $status['class'] ?>">
                                                    <?= $status['label'] ?>
                                                </span>
                                            </td>
                                            <td><span class="text-muted small"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span></td>
                                            <td class="text-end">
                                                <a href="/admin/portal-users/<?= $user['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Documents -->
        <?php if (!empty($documents)): ?>
            <div class="col-12">
                <div class="nd-card h-100">
                    <div class="nd-card-header d-flex align-items-center gap-2">
                        <i class="bi bi-folder text-warning"></i>
                        <h5 class="nd-card-title mb-0">Documentos Gerais (<?= count($documents) ?>)</h5>
                    </div>
                    <div class="nd-card-body p-0">
                        <div class="table-responsive">
                            <table class="nd-table">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Arquivo</th>
                                        <th>Descrição</th>
                                        <th>Data</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-medium text-dark"><?= htmlspecialchars($doc['title']) ?></div>
                                            </td>
                                            <td>
                                                <code class="small text-muted"><?= htmlspecialchars($doc['file_name'] ?? '-') ?></code>
                                            </td>
                                            <td>
                                                <span class="text-muted small">
                                                    <?= htmlspecialchars(mb_substr($doc['description'] ?? '', 0, 50)) ?>...
                                                </span>
                                            </td>
                                            <td><span class="text-muted small"><?= date('d/m/Y', strtotime($doc['created_at'])) ?></span></td>
                                            <td class="text-end">
                                                <a href="/admin/general-documents/<?= $doc['id'] ?>/edit" class="nd-btn nd-btn-outline nd-btn-sm" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
