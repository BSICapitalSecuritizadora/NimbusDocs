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
                <h2 class="nd-card-title">Histórico de Eventos</h2>
            </div>
            <div class="nd-card-body">
                <div class="nd-timeline">
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

                    <!-- Notes Loop -->
                    <?php if (!empty($notes)): ?>
                        <?php foreach ($notes as $note): ?>
                            <div class="nd-timeline-item">
                                <div class="nd-timeline-marker admin"></div>
                                <div class="nd-timeline-content">
                                    <div class="nd-timeline-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="nd-timeline-author">Equipe de Análise</span>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size: 0.65rem;">ADMIN</span>
                                        </div>
                                        <span class="nd-timeline-date">
                                            <?php
                                            $noteDate = $note['created_at'] ?? '';
                                            if ($noteDate) {
                                                try {
                                                    echo (new DateTime($noteDate))->format('d/m/Y H:i');
                                                } catch (Exception $e) { echo $noteDate; }
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="text-dark small">
                                        <?= nl2br(htmlspecialchars($note['message'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                                    </div>
                                </div>
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