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
    'PENDING' => ['label' => 'Pendente', 'class' => 'warning'],
    'UNDER_REVIEW' => ['label' => 'Em Análise', 'class' => 'info'],
    'COMPLETED' => ['label' => 'Concluída', 'class' => 'success'],
    'REJECTED' => ['label' => 'Rejeitada', 'class' => 'danger'],
];

$userStatusLabels = [
    'INVITED' => ['label' => 'Convidado', 'class' => 'secondary'],
    'ACTIVE' => ['label' => 'Ativo', 'class' => 'success'],
    'INACTIVE' => ['label' => 'Inativo', 'class' => 'warning'],
    'BLOCKED' => ['label' => 'Bloqueado', 'class' => 'danger'],
];
?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-search me-2"></i>
                        Resultados da Busca
                    </h4>
                    <p class="text-muted mb-0">
                        <?php if ($total > 0): ?>
                            <?= $total ?> resultado(s) para "<strong><?= htmlspecialchars($query) ?></strong>"
                        <?php else: ?>
                            Nenhum resultado para "<strong><?= htmlspecialchars($query) ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="/admin/dashboard" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if ($total === 0): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum resultado encontrado</h5>
                        <p class="text-muted">Tente buscar por outros termos ou verifique a ortografia.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Submissions -->
            <?php if (!empty($submissions)): ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                Submissões (<?= count($submissions) ?>)
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Título</th>
                                            <th>Usuário</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $sub): ?>
                                            <?php 
                                                $status = $statusLabels[$sub['status']] ?? ['label' => $sub['status'], 'class' => 'secondary'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <code><?= htmlspecialchars($sub['reference_code']) ?></code>
                                                </td>
                                                <td><?= htmlspecialchars($sub['title']) ?></td>
                                                <td>
                                                    <div><?= htmlspecialchars($sub['user_name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($sub['user_email']) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $status['class'] ?>">
                                                        <?= $status['label'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($sub['submitted_at'])) ?></td>
                                                <td>
                                                    <a href="/admin/submissions/<?= $sub['id'] ?>" class="btn btn-sm btn-outline-primary">
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
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="bi bi-people me-2 text-success"></i>
                                Usuários do Portal (<?= count($users) ?>)
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>Documento</th>
                                            <th>Status</th>
                                            <th>Cadastro</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <?php 
                                                $status = $userStatusLabels[$user['status']] ?? ['label' => $user['status'], 'class' => 'secondary'];
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                                <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($user['document_number'] ?? '-') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $status['class'] ?>">
                                                        <?= $status['label'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <a href="/admin/portal-users/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
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
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="bi bi-folder me-2 text-warning"></i>
                                Documentos Gerais (<?= count($documents) ?>)
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Título</th>
                                            <th>Arquivo</th>
                                            <th>Descrição</th>
                                            <th>Data</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $doc): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($doc['title']) ?></td>
                                                <td><code><?= htmlspecialchars($doc['file_name'] ?? '-') ?></code></td>
                                                <td><?= htmlspecialchars(mb_substr($doc['description'] ?? '', 0, 50)) ?>...</td>
                                                <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                                <td>
                                                    <a href="/admin/general-documents/<?= $doc['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
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
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
