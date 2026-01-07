<?php
/**
 * Espera em $viewData:
 * - array $documents
 * - string $csrfToken
 */
$documents = $documents ?? [];
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-folder-fill text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Documentos do Portal</h1>
            <p class="text-muted mb-0 small">Gerencie os documentos enviados pelos usuários</p>
        </div>
    </div>
    <a href="/admin/documents/new" class="nd-btn nd-btn-primary nd-btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        Novo documento
    </a>
</div>

<!-- Documents List -->
<div class="nd-card">
    <div class="nd-card-body p-0">
         <?php if (!$documents): ?>
            <div class="text-center py-5">
                <i class="bi bi-folder-x text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="text-muted mb-2">Nenhum documento encontrado.</p>
                <a href="/admin/documents/new" class="btn btn-link text-decoration-none p-0">
                    Enviar o primeiro documento
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Documento</th>
                            <th>Arquivo</th>
                            <th>Data</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $d): ?>
                        <tr>
                            <td><span class="text-muted small">#<?= (int)$d['id'] ?></span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="nd-avatar nd-avatar-sm nd-avatar-initials" style="background: var(--nd-primary-100); color: var(--nd-primary-700);">
                                        <?= strtoupper(substr($d['user_full_name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div class="d-flex flex-column" style="line-height:1.2;">
                                        <span class="fw-medium text-dark"><?= htmlspecialchars($d['user_full_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($d['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block fw-medium text-dark"><?= htmlspecialchars($d['title'] ?? 'Sem título', ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span class="text-dark font-monospace"><?= htmlspecialchars($d['file_original_name'] ?? 'arquivo', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($d['created_at'])) ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="/admin/documents/<?= (int)$d['id'] ?>" class="nd-btn nd-btn-outline nd-btn-sm" title="Visualizar Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="post" action="/admin/documents/<?= (int)$d['id'] ?>/delete" onsubmit="return confirm('Excluir este documento?');" class="d-inline">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        <button class="nd-btn nd-btn-outline nd-btn-sm text-danger border-start-0" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                 </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
