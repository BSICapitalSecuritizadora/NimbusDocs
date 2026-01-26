<?php

/** @var array $submission */
/** @var array $userFiles */
/** @var array $responseFiles */
/** @var array $notes */
/** @var string $csrfToken */

$statusClass = match($submission['status'] ?? '') {
    'PENDING' => 'warning',
    'UNDER_REVIEW' => 'info',
    'COMPLETED', 'APPROVED' => 'success',
    'REJECTED' => 'danger',
    default => 'secondary'
};

$statusLabel = match($submission['status'] ?? '') {
    'PENDING' => 'Pendente',
    'UNDER_REVIEW' => 'Em Análise',
    'COMPLETED' => 'Concluído',
    'APPROVED' => 'Aprovado',
    'REJECTED' => 'Rejeitado',
    default => $submission['status'] ?? '-'
};

$statusIcon = match($submission['status'] ?? '') {
    'PENDING' => 'bi-hourglass-split',
    'UNDER_REVIEW' => 'bi-search',
    'COMPLETED', 'APPROVED' => 'bi-check-circle-fill',
    'REJECTED' => 'bi-x-circle-fill',
    default => 'bi-circle'
};
?>

<div class="d-flex flex-column gap-4">

    <!-- Informações da Submissão e Alteração de Status -->
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text" style="color: var(--nd-navy-500);"></i>
                <h2 class="nd-card-title mb-0">Detalhes do Envio</h2>
            </div>
            <span class="nd-badge nd-badge-<?= $statusClass ?>">
                <i class="bi <?= $statusIcon ?> me-1"></i>
                <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <div class="nd-card-body">
            <!-- Info Grid -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <label class="nd-label text-muted small mb-1">Protocolo</label>
                            <div class="fw-semibold">
                                <code class="px-2 py-1 rounded bg-light text-dark border"><?= htmlspecialchars($submission['reference_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></code>
                            </div>
                        </div>
                        <div>
                            <label class="nd-label text-muted small mb-1">Assunto</label>
                            <div class="fw-semibold text-dark"><?= htmlspecialchars($submission['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <?php if (!empty($submission['description'])): ?>
                        <div>
                            <label class="nd-label text-muted small mb-1">Descrição</label>
                            <div class="text-secondary"><?= nl2br(htmlspecialchars($submission['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <label class="nd-label text-muted small mb-1">Solicitante</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="nd-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    <?= strtoupper(substr($submission['user_full_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-medium"><?= htmlspecialchars($submission['user_full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($submission['user_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="nd-label text-muted small mb-1">Data do Envio</label>
                            <div class="d-flex align-items-center gap-2 text-secondary">
                                <i class="bi bi-calendar3"></i>
                                <?php
                                $submittedAt = $submission['submitted_at'] ?? '';
                                if ($submittedAt) {
                                    try {
                                        $date = new DateTime($submittedAt);
                                        echo $date->format('d/m/Y \à\s H:i');
                                    } catch (Exception $e) {
                                        echo htmlspecialchars($submittedAt, ENT_QUOTES, 'UTF-8');
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Formulário de Alteração de Status -->
            <div class="p-4 rounded" style="background: linear-gradient(135deg, var(--nd-gray-50) 0%, rgba(212, 168, 75, 0.05) 100%); border: 1px solid var(--nd-gray-200);">
                <h3 class="h6 fw-semibold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat" style="color: var(--nd-gold-500);"></i>
                    Alterar Situação
                </h3>
                
                <form method="post" action="/admin/submissions/<?= (int)$submission['id'] ?>/status">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="nd-label" for="status">Nova Situação</label>
                            <select class="nd-input" id="status" name="status" required>
                                <option value="PENDING" <?= ($submission['status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>
                                    ⏳ Pendente
                                </option>
                                <option value="UNDER_REVIEW" <?= ($submission['status'] ?? '') === 'UNDER_REVIEW' ? 'selected' : '' ?>>
                                    🔍 Em Análise
                                </option>
                                <option value="COMPLETED" <?= in_array($submission['status'] ?? '', ['COMPLETED', 'APPROVED']) ? 'selected' : '' ?>>
                                    ✅ Aprovado/Concluído
                                </option>
                                <option value="REJECTED" <?= ($submission['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>
                                    ❌ Rejeitado
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="nd-label" for="visibility">Visibilidade da Nota</label>
                            <select class="nd-input" id="visibility" name="visibility">
                                <option value="USER_VISIBLE">👁️ Visível ao solicitante</option>
                                <option value="ADMIN_ONLY">🔒 Apenas administradores</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="nd-label" for="note">Observação (opcional)</label>
                            <textarea class="nd-input" id="note" name="note" rows="3" 
                                placeholder="Adicione uma observação sobre esta alteração de status..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="nd-btn nd-btn-primary" onclick="document.getElementById('status').value='COMPLETED';">
                                    <i class="bi bi-check-lg me-1"></i> Aprovar
                                </button>
                                <button type="submit" class="nd-btn nd-btn-outline" style="border-color: var(--nd-danger); color: var(--nd-danger);" 
                                    onclick="document.getElementById('status').value='REJECTED';">
                                    <i class="bi bi-x-lg me-1"></i> Rejeitar
                                </button>
                                <button type="submit" class="nd-btn nd-btn-gold">
                                    <i class="bi bi-save me-1"></i> Salvar Alteração
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Histórico de Notas -->
    <?php if ($notes): ?>
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-chat-left-text" style="color: var(--nd-info);"></i>
            <h2 class="nd-card-title mb-0">Histórico de Observações</h2>
            <span class="nd-badge nd-badge-secondary ms-2"><?= count($notes) ?></span>
        </div>
        <div class="nd-card-body">
            <div class="d-flex flex-column gap-3">
                <?php foreach ($notes as $note): ?>
                    <div class="p-3 rounded <?= ($note['visibility'] ?? '') === 'ADMIN_ONLY' ? 'border-start border-3 border-warning' : '' ?>" 
                         style="background: var(--nd-gray-50); border: 1px solid var(--nd-gray-200);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="nd-avatar" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                    <?= strtoupper(substr($note['admin_name'] ?? 'A', 0, 1)) ?>
                                </div>
                                <div>
                                    <span class="fw-medium small"><?= htmlspecialchars($note['admin_name'] ?? 'Administrador', ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if (($note['visibility'] ?? '') === 'ADMIN_ONLY'): ?>
                                        <span class="nd-badge nd-badge-warning ms-2" style="font-size: 0.6rem;">Interno</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                <?php
                                $noteDate = $note['created_at'] ?? '';
                                if ($noteDate) {
                                    try {
                                        $d = new DateTime($noteDate);
                                        echo $d->format('d/m/Y H:i');
                                    } catch (Exception $e) {
                                        echo htmlspecialchars($noteDate, ENT_QUOTES, 'UTF-8');
                                    }
                                }
                                ?>
                            </small>
                        </div>
                        <div class="text-secondary small">
                            <?= nl2br(htmlspecialchars($note['message'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Arquivos do Usuário -->
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-up" style="color: var(--nd-navy-500);"></i>
            <h2 class="nd-card-title mb-0">Anexos Recebidos</h2>
            <?php if ($userFiles): ?>
                <span class="nd-badge nd-badge-secondary ms-2"><?= count($userFiles) ?></span>
            <?php endif; ?>
        </div>
        <div class="nd-card-body">
            <?php if (!$userFiles): ?>
                <div class="text-center py-4">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2 small">Nenhum anexo enviado.</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($userFiles as $f): ?>
                        <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: var(--nd-gray-50); border: 1px solid var(--nd-gray-200);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="nd-avatar" style="width: 40px; height: 40px; background: rgba(26, 41, 66, 0.1); color: var(--nd-navy-700);">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div>
                                    <div class="fw-medium text-dark"><?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="small text-muted">
                                        <?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB
                                    </div>
                                </div>
                            </div>
                            <a href="/admin/files/<?= (int)$f['id'] ?>/download" class="nd-btn nd-btn-outline nd-btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Documentos de Resposta -->
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-send" style="color: var(--nd-gold-500);"></i>
            <h2 class="nd-card-title mb-0">Documentos de Retorno</h2>
            <?php if ($responseFiles): ?>
                <span class="nd-badge nd-badge-secondary ms-2"><?= count($responseFiles) ?></span>
            <?php endif; ?>
        </div>
        <div class="nd-card-body">
            <?php if ($responseFiles): ?>
                <div class="d-flex flex-column gap-2 mb-4">
                    <?php foreach ($responseFiles as $f): ?>
                        <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: var(--nd-gray-50); border: 1px solid var(--nd-gray-200);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="nd-avatar" style="width: 40px; height: 40px; background: rgba(212, 168, 75, 0.15); color: var(--nd-gold-600);">
                                    <i class="bi bi-file-earmark-check"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-medium text-dark"><?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ((int)$f['visible_to_user'] === 1): ?>
                                            <span class="nd-badge nd-badge-success" style="font-size: 0.65rem;">Disponível no Portal</span>
                                        <?php else: ?>
                                            <span class="nd-badge nd-badge-secondary" style="font-size: 0.65rem;">Restrito (Interno)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB
                                    </div>
                                </div>
                            </div>
                            <a href="/admin/files/<?= (int)$f['id'] ?>/download" class="nd-btn nd-btn-outline nd-btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="mb-4">
                    <p class="text-muted small mb-0">Nenhum documento de retorno registrado.</p>
                </div>
            <?php endif; ?>

            <div class="p-3 rounded" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                <h3 class="h6 mb-3 fw-semibold text-dark">
                    <i class="bi bi-cloud-upload me-2 text-muted"></i>Anexar Resposta
                </h3>
                
                <form method="post"
                    action="/admin/submissions/<?= (int)$submission['id'] ?>/response-files"
                    enctype="multipart/form-data">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <input type="file" name="response_files[]" class="form-control" multiple>
                        <div class="form-text small mt-2 text-muted">
                            Formatos aceitos: PDF, DOCX, XLSX, ZIP, imagens.
                        </div>
                    </div>

                    <button type="submit" class="nd-btn nd-btn-gold nd-btn-sm">
                        <i class="bi bi-send me-2"></i>Transmitir Arquivos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>