<?php

/** @var array $submission */
/** @var array $userFiles */
/** @var array $responseFiles */
/** @var array $notes */
/** @var string $csrfToken */

?>

<div class="d-flex flex-column gap-4">

    <!-- Arquivos do Usuário -->
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-person-up" style="color: var(--nd-navy-500);"></i>
            <h2 class="nd-card-title mb-0">Arquivos enviados pelo usuário</h2>
        </div>
        <div class="nd-card-body">
            <?php if (!$userFiles): ?>
                <div class="text-center py-4">
                    <i class="bi bi-file-earmark-x text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2 small">Nenhum arquivo anexado pelo usuário.</p>
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
                                <i class="bi bi-download"></i> Baixar
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
            <h2 class="nd-card-title mb-0">Documentos enviados ao usuário</h2>
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
                                            <span class="nd-badge nd-badge-success" style="font-size: 0.65rem;">Visível</span>
                                        <?php else: ?>
                                            <span class="nd-badge nd-badge-secondary" style="font-size: 0.65rem;">Oculto</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB
                                    </div>
                                </div>
                            </div>
                            <a href="/admin/files/<?= (int)$f['id'] ?>/download" class="nd-btn nd-btn-outline nd-btn-sm">
                                <i class="bi bi-download"></i> Baixar
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="mb-4">
                    <p class="text-muted small mb-0">Nenhum documento de retorno enviado ainda.</p>
                </div>
            <?php endif; ?>

            <div class="p-3 rounded" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                <h3 class="h6 mb-3 fw-semibold text-dark">
                    <i class="bi bi-cloud-upload me-2 text-muted"></i>Enviar novos documentos
                </h3>
                
                <form method="post"
                    action="/admin/submissions/<?= (int)$submission['id'] ?>/response-files"
                    enctype="multipart/form-data">
                    <input type="hidden" name="_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <input type="file" name="response_files[]" class="form-control" multiple>
                        <div class="form-text small mt-2 text-muted">
                            Tipos comuns: PDF, DOCX, XLSX, ZIP, imagens.
                        </div>
                    </div>

                    <button type="submit" class="nd-btn nd-btn-gold nd-btn-sm">
                        <i class="bi bi-send me-2"></i>Enviar documentos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>