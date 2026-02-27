<?php

use App\Support\StatusHelper;

/** 
 * @var array{id: int, title: string, reference_code: string, status: string, submitted_at?: string|null, message?: string, ...} $submission
 * @var array<int, array{original_name: string, size_bytes?: int, document_type?: string, id?: int}> $files
 * @var array<int, array{created_at: string, message: string}> $notes
 * @var array<int, array> $responseFiles
 * @var array<int, array{name: string, document_rg?: string, document_cnpj?: string, percentage?: float|string}> $shareholders 
 */

// Status translations via helper centralizado
$statusRaw = $submission['status'] ?? '';
$statusInfo = StatusHelper::translate($statusRaw);
$statusLabel = $statusInfo['label'];
$badgeClass = $statusInfo['badge'];
$icon = $statusInfo['icon'];

$dateFormatted = !empty($submission['submitted_at']) 
    ? date('d/m/Y \à\s H:i', strtotime($submission['submitted_at'])) 
    : 'N/A';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="/portal/submissions" class="nd-btn nd-btn-sm nd-btn-outline mb-2">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <h1 class="h3 fw-bold text-dark mb-0">Detalhes do Protocolo <span class="text-secondary">#<?= htmlspecialchars($submission['reference_code'], ENT_QUOTES, 'UTF-8') ?></span></h1>
    </div>
    <div class="text-end">
        <span class="nd-badge <?= $badgeClass ?> fs-6 px-3 py-2 rounded-pill">
            <i class="bi <?= $icon ?> me-2"></i>
            <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Main details -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex justify-content-between align-items-center">
                <h2 class="nd-card-title">Dados da Empresa</h2>
                <span class="small text-muted"><i class="bi bi-building me-1"></i> Identificação</span>
            </div>
            <div class="nd-card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="nd-label text-muted small mb-1">Razão Social</label>
                        <div class="fw-semibold text-dark fs-5">
                            <?= htmlspecialchars($submission['company_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <label class="nd-label text-muted small mb-1">Assunto da Submissão</label>
                         <div class="text-dark fw-medium">
                            <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?>
                         </div>
                    </div>
                </div>

                <div class="row g-4">
                     <!-- 4-Column Grid for Metrics -->
                    <div class="col-6 col-md-3">
                        <label class="nd-label text-muted small mb-1">CNPJ</label>
                        <div class="font-monospace text-dark">
                            <?php 
                            $cnpj = $submission['company_cnpj'] ?? '';
                            echo htmlspecialchars(preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj), ENT_QUOTES, 'UTF-8');
                            ?>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="nd-label text-muted small mb-1">Telefone</label>
                        <div class="text-dark">
                             <?= htmlspecialchars($submission['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="nd-label text-muted small mb-1">Faturamento</label>
                        <div class="text-dark">
                            R$ <?= number_format((float)($submission['annual_revenue'] ?? 0), 2, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="nd-label text-muted small mb-1">Patrimômio Líq.</label>
                        <div class="text-dark">
                            R$ <?= number_format((float)($submission['net_worth'] ?? 0), 2, ',', '.') ?>
                        </div>
                    </div>
                </div>

                <hr class="border-light-subtle my-4">

                <div class="row g-4">
                     <div class="col-12">
                         <h6 class="text-secondary small fw-bold text-uppercase mb-3">Solicitante & Compliance</h6>
                     </div>
                     <div class="col-md-4">
                        <label class="nd-label text-muted small mb-1">Solicitante</label>
                        <div class="text-dark fw-medium">
                            <?= htmlspecialchars($submission['registrant_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="small text-secondary">
                             <?= htmlspecialchars($submission['registrant_position'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                     </div>
                     <div class="col-md-4">
                         <label class="nd-label text-muted small mb-1">CPF</label>
                         <div class="font-monospace text-secondary">
                             <?php 
                             $cpf = $submission['registrant_cpf'] ?? '';
                             echo htmlspecialchars(preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf), ENT_QUOTES, 'UTF-8');
                             ?>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center">
                                <i class="bi <?= !empty($submission['is_us_person']) ? 'bi-check-circle-fill text-warning' : 'bi-dash-circle text-muted' ?> me-2"></i>
                                <span class="small text-secondary">US Person</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi <?= !empty($submission['is_pep']) ? 'bi-check-circle-fill text-danger' : 'bi-dash-circle text-muted' ?> me-2"></i>
                                <span class="small text-secondary">PEP</span>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <!-- History/Notes Timeline -->
         <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h2 class="nd-card-title">Interações e Histórico</h2>
            </div>
            <div class="nd-card-body">
                <?php if (($submission['status'] ?? '') === 'NEEDS_CORRECTION'): ?>
                    <div class="alert alert-warning border border-warning-subtle rounded-3 mb-4 p-4 text-dark shadow-sm">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                            <h5 class="mb-0 fw-bold">Ação Necessária</h5>
                        </div>
                        <p class="mb-3">Foram solicitadas correções em sua submissão. Por favor, leia os comentários abaixo, anexe o documento corrigido caso necessário, e envie sua resposta.</p>
                        
                        <form action="/portal/submissions/<?= (int)$submission['id'] ?>/reply" method="POST" enctype="multipart/form-data" class="bg-white p-3 rounded border">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="mb-3">
                                <label class="nd-label" for="comment">Resposta / Comentário</label>
                                <textarea name="comment" id="comment" rows="2" class="nd-input" placeholder="Descreva as correções feitas..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="nd-label fw-medium mb-2 d-block">Documento Corrigido <span class="text-muted fw-normal">(Opcional)</span></label>
                                <div class="position-relative">
                                    <input type="file" name="file" id="file" class="d-none" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Nenhum arquivo selecionado'">
                                    <label for="file" class="d-flex align-items-center gap-3 p-3 border rounded-3 bg-white" style="cursor: pointer; border-style: dashed !important; border-width: 2px !important; border-color: #dee2e6 !important; transition: all 0.2s; hover: border-color: var(--nd-primary) !important;">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                            <i class="bi bi-cloud-arrow-up-fill fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="text-dark fw-semibold mb-0">Clique para anexar um documento</div>
                                            <div class="text-muted small text-truncate mt-1" id="file-name">Nenhum arquivo selecionado</div>
                                        </div>
                                        <div class="text-primary fw-medium small px-3 py-1 rounded-pill" style="background-color: rgba(13, 110, 253, 0.1);">Procurar</div>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="nd-btn nd-btn-primary">
                                <i class="bi bi-send me-2"></i> Enviar Correção
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php 
                $timeline = [];
                $allNotes = $notes ?? [];
                $allComments = $comments ?? [];
                $allHistory = $statusHistory ?? [];
                
                foreach ($allNotes as $note) {
                    $timeline[] = ['type' => 'note', 'created_at' => $note['created_at'], 'data' => $note];
                }
                foreach ($allComments as $comment) {
                    $timeline[] = ['type' => 'comment', 'created_at' => $comment['created_at'], 'data' => $comment];
                }
                foreach ($allHistory as $history) {
                    if (($history['old_status'] ?? '') === '' && $history['new_status'] === 'PENDING') continue;
                    $timeline[] = ['type' => 'status', 'created_at' => $history['created_at'], 'data' => $history];
                }
                
                usort($timeline, function($a, $b) {
                    return strtotime($a['created_at']) <=> strtotime($b['created_at']);
                });
                ?>

                <div class="nd-timeline mt-3">
                    <!-- Submission Event (Start) -->
                    <div class="nd-timeline-item">
                        <div class="nd-timeline-marker user"></div>
                        <div class="nd-timeline-content border-0 shadow-none bg-light ps-0 py-0">
                            <div class="nd-timeline-header mb-1">
                                <span class="nd-timeline-author text-primary">Envio Realizado</span>
                                <span class="nd-timeline-date"><?= $dateFormatted ?></span>
                            </div>
                            <p class="mb-0 text-secondary small">
                                Protocolo criado por <?= htmlspecialchars($submission['registrant_name'] ?? 'Usuário', ENT_QUOTES, 'UTF-8') ?>.
                            </p>
                            <?php if (!empty($submission['message'])): ?>
                                <div class="mt-2 p-2 bg-white border rounded text-muted fst-italic small">
                                    "<?= nl2br(htmlspecialchars($submission['message'], ENT_QUOTES, 'UTF-8')) ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Timeline Loop -->
                    <?php if (!empty($timeline)): ?>
                        <?php foreach ($timeline as $item): ?>
                            <div class="nd-timeline-item">
                                <?php
                                $d = new DateTime($item['created_at']);
                                $fmtDate = $d->format('d/m/Y H:i');
                                
                                if ($item['type'] === 'status') {
                                    $history = $item['data'];
                                    $newStatusLabel = match($history['new_status']) {
                                        'PENDING' => 'Pendente',
                                        'UNDER_REVIEW' => 'Em Análise',
                                        'NEEDS_CORRECTION' => 'Aguardando Correção',
                                        'COMPLETED', 'APPROVED' => 'Aprovado',
                                        'REJECTED' => 'Rejeitado',
                                        default => $history['new_status']
                                    };
                                    $actorName = htmlspecialchars($history['changed_by_name'] ?? 'Sistema', ENT_QUOTES, 'UTF-8');
                                    $isAdmin = ($history['changed_by_type'] ?? '') === 'ADMIN';
                                    $markerClass = $isAdmin ? 'admin' : 'user';
                                ?>
                                    <div class="nd-timeline-marker <?= $markerClass ?>"></div>
                                    <div class="nd-timeline-content py-0 border-0 bg-transparent shadow-none ps-0">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <span class="nd-timeline-author text-secondary"><?= $actorName ?></span>
                                            <span class="text-muted small">alterou o status para</span>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><?= $newStatusLabel ?></span>
                                            <span class="nd-timeline-date ms-auto"><?= $fmtDate ?></span>
                                        </div>
                                    </div>
                                <?php
                                } elseif ($item['type'] === 'note' || $item['type'] === 'comment') {
                                    $data = $item['data'];
                                    $isLegacy = ($item['type'] === 'note');
                                    
                                    if ($isLegacy) {
                                        $authorName = htmlspecialchars($data['admin_name'] ?? 'Equipe de Análise', ENT_QUOTES, 'UTF-8');
                                        $markerClass = 'admin';
                                        $pillName = 'ADMIN';
                                    } else {
                                        $authorName = htmlspecialchars($data['author_name'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');
                                        $isAdmin = ($data['author_type'] === 'ADMIN');
                                        $markerClass = $isAdmin ? 'admin' : 'user';
                                        $pillName = $isAdmin ? 'ADMIN' : 'VOCÊ';
                                    }
                                    
                                    $requiresAction = !$isLegacy && !empty($data['requires_action']);
                                ?>
                                    <div class="nd-timeline-marker <?= $markerClass ?>"></div>
                                    <div class="nd-timeline-content <?= $requiresAction ? 'border-warning border-start border-3' : '' ?>">
                                        <div class="nd-timeline-header">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="nd-timeline-author"><?= $authorName ?></span>
                                                <span class="badge <?= $markerClass === 'admin' ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-secondary-subtle text-secondary border-secondary-subtle' ?> rounded-pill px-2" style="font-size: 0.65rem; border: 1px solid;"><?= $pillName ?></span>
                                                <?php if ($requiresAction): ?>
                                                    <span class="badge bg-danger text-white rounded-pill px-2" style="font-size: 0.65rem;">Ação Necessária</span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="nd-timeline-date"><?= $fmtDate ?></span>
                                        </div>
                                        <div class="text-dark small" style="white-space: pre-wrap;"><?= htmlspecialchars($data['comment'] ?? $data['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Attachments -->
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex justify-content-between align-items-center">
                 <h2 class="nd-card-title">Anexos</h2>
                 <span class="badge bg-secondary text-white rounded-pill"><?= count($files) ?></span>
            </div>
            <div class="nd-card-body p-0">
                <?php if (!$files): ?>
                    <div class="p-4 text-center text-muted small">
                        Nenhum documento enviado.
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($files as $f): 
                             $docLabels = [
                                'BALANCE_SHEET' => 'Último Balanço',
                                'DRE' => 'DRE',
                                'POLICIES' => 'Políticas',
                                'CNPJ_CARD' => 'Cartão CNPJ',
                                'POWER_OF_ATTORNEY' => 'Procuração',
                                'MINUTES' => 'Ata',
                                'ARTICLES_OF_INCORPORATION' => 'Contrato Social',
                                'BYLAWS' => 'Estatuto',
                            ];
                            $label = $docLabels[$f['document_type'] ?? ''] ?? $f['original_name'];
                            $ext = pathinfo($f['original_name'], PATHINFO_EXTENSION);
                        ?>
                        <div class="list-group-item d-flex align-items-center px-4 py-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="text-dark fw-medium text-truncate" title="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">
                                    <?= strtoupper($ext) ?> &bull; <?= isset($f['size_bytes']) ? number_format((int)$f['size_bytes'] / 1024, 0, ',', '.') . ' KB' : '-' ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Shareholders Summary (Compact) -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h2 class="nd-card-title">Sócios</h2>
            </div>
            <div class="nd-card-body p-0">
                <?php if (empty($shareholders)): ?>
                     <div class="p-3 text-center text-muted small">Nenhum sócio.</div>
                <?php else: ?>
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 border-0 small text-secondary">Nome</th>
                                <th class="pe-3 border-0 small text-secondary text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shareholders as $sh): ?>
                            <tr>
                                <td class="ps-3 border-bottom-0">
                                    <div class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($sh['name'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($sh['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </td>
                                <td class="pe-3 border-bottom-0 text-end fw-medium">
                                    <?= number_format((float)($sh['percentage'] ?? 0), 2, ',', '.') ?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Response Files -->
        <?php if ($responseFiles): ?>
            <div class="nd-card border-primary-subtle shadow-sm">
                <div class="nd-card-header bg-primary-subtle bg-opacity-10 border-bottom border-primary-subtle">
                     <h2 class="nd-card-title text-primary-emphasis mb-0">
                        Documentos Disponíveis
                     </h2>
                </div>
                <div class="nd-card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($responseFiles as $fr): ?>
                        <div class="list-group-item px-3 py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center overflow-hidden">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-4 me-3"></i>
                                <div class="overflow-hidden">
                                     <div class="fw-medium text-dark text-truncate" style="max-width: 140px;" title="<?= htmlspecialchars($fr['original_name'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($fr['original_name'], ENT_QUOTES, 'UTF-8') ?>
                                     </div>
                                </div>
                            </div>
                            <a href="/portal/files/<?= (int)$fr['id'] ?>/download" class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="bi bi-download me-1"></i> Baixar
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>