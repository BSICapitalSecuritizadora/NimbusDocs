<?php
/** @var array $submission */
/** @var array $files */
/** @var array $notes */
/** @var array $responseFiles */

// Status translations & format
$statusRaw = $submission['status'] ?? '';
$statusLabel = $statusRaw;
$badgeClass = 'nd-badge-secondary';
$icon = 'bi-circle';

switch ($statusRaw) {
    case 'PENDING':
        $statusLabel = 'Pendente';
        $badgeClass = 'nd-badge-warning';
        $icon = 'bi-hourglass';
        break;
    case 'IN_REVIEW':
        $statusLabel = 'Em Análise';
        $badgeClass = 'nd-badge-info';
        $icon = 'bi-search';
        break;
    case 'APPROVED':
    case 'COMPLETED':
    case 'FINALIZADA':
        $statusLabel = 'Concluído';
        $badgeClass = 'nd-badge-success';
        $icon = 'bi-check2-circle';
        break;
    case 'REJECTED':
    case 'REJEITADA':
        $statusLabel = 'Rejeitado';
        $badgeClass = 'nd-badge-danger';
        $icon = 'bi-x-circle';
        break;
}

$dateFormatted = !empty($submission['submitted_at']) 
    ? date('d/m/Y \à\s H:i', strtotime($submission['submitted_at'])) 
    : 'N/A';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="/portal/submissions" class="nd-btn nd-btn-sm nd-btn-outline mb-2">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <h1 class="h3 fw-bold text-dark mb-0">Detalhes do Envio</h1>
    </div>
    <div class="text-end">
        <span class="nd-badge <?= $badgeClass ?> fs-6 px-3 py-2">
            <i class="bi <?= $icon ?> me-2"></i>
            <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Main details -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h2 class="nd-card-title">Resumo do Protocolo</h2>
            </div>
            <div class="nd-card-body">
                <div class="row gy-3">
                    <div class="col-12">
                        <label class="nd-label text-muted mb-1">Assunto / Título</label>
                        <div class="fw-semibold fs-5 text-dark">
                            <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>
                
                <hr class="border-light-subtle my-3">
                
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Protocolo</label>
                        <div class="font-monospace text-secondary bg-light rounded px-2 py-1 d-inline-block">
                            <?= htmlspecialchars($submission['reference_code'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Data de Envio</label>
                        <div class="text-dark">
                            <i class="bi bi-calendar3 me-1 text-secondary"></i>
                            <?= $dateFormatted ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h2 class="nd-card-title">Detalhes da Empresa</h2>
            </div>
            <div class="nd-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Empresa</label>
                        <div class="text-dark fw-medium">
                            <?= htmlspecialchars($submission['company_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">CNPJ</label>
                        <div class="text-dark font-monospace">
                            <?php 
                            $cnpj = $submission['company_cnpj'] ?? '';
                            echo htmlspecialchars(preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj), ENT_QUOTES, 'UTF-8');
                            ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Responsável</label>
                        <div class="text-dark">
                            <?= htmlspecialchars($submission['responsible_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Telefone</label>
                        <div class="text-dark">
                            <?= htmlspecialchars($submission['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="nd-label text-muted mb-1">Atividade Principal</label>
                        <div class="text-dark">
                            <?= htmlspecialchars($submission['main_activity'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Patrimônio Líquido</label>
                        <div class="text-dark">
                            R$ <?= number_format((float)($submission['net_worth'] ?? 0), 2, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="nd-label text-muted mb-1">Faturamento Anual</label>
                        <div class="text-dark">
                            R$ <?= number_format((float)($submission['annual_revenue'] ?? 0), 2, ',', '.') ?>
                        </div>
                    </div>
                    
                    <div class="col-12 border-top border-light-subtle pt-3 mt-3">
                        <h6 class="text-secondary small fw-bold text-uppercase mb-3">Seus Dados</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="nd-label text-muted mb-1">Nome Completo</label>
                                <div class="text-dark">
                                    <?= htmlspecialchars($submission['registrant_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="nd-label text-muted mb-1">Cargo</label>
                                <div class="text-dark">
                                    <?= htmlspecialchars($submission['registrant_position'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="nd-label text-muted mb-1">RG</label>
                                <div class="text-dark">
                                    <?= htmlspecialchars($submission['registrant_rg'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="nd-label text-muted mb-1">CPF</label>
                                <div class="text-dark font-monospace">
                                    <?php 
                                    $cpf = $submission['registrant_cpf'] ?? '';
                                    echo htmlspecialchars(preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf), ENT_QUOTES, 'UTF-8');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 border-top border-light-subtle pt-3 mt-3">
                         <div class="d-flex gap-4">
                            <div class="d-flex align-items-center">
                                <i class="bi <?= !empty($submission['is_us_person']) ? 'bi-check-circle-fill text-warning' : 'bi-x-circle text-muted' ?> me-2"></i>
                                <span class="small text-secondary">US Person</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi <?= !empty($submission['is_pep']) ? 'bi-check-circle-fill text-danger' : 'bi-x-circle text-muted' ?> me-2"></i>
                                <span class="small text-secondary">PEP (Pessoa Exposta Politicamente)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shareholders -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h2 class="nd-card-title">Quadro Societário</h2>
            </div>
            <div class="nd-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-secondary small text-uppercase">Nome</th>
                                <th class="px-4 py-3 text-secondary small text-uppercase">RG</th>
                                <th class="px-4 py-3 text-secondary small text-uppercase">CNPJ</th>
                                <th class="px-4 py-3 text-secondary small text-uppercase text-end">Participação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shareholders)): ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-muted">Nenhum sócio cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($shareholders as $shareholder): ?>
                                    <tr>
                                        <td class="px-4 py-3 fw-medium text-dark">
                                            <?= htmlspecialchars($shareholder['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="px-4 py-3 text-secondary font-monospace">
                                            <?= htmlspecialchars($shareholder['document_rg'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="px-4 py-3 text-secondary font-monospace">
                                            <?php 
                                            $sCnpj = $shareholder['document_cnpj'] ?? '';
                                            echo htmlspecialchars(preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $sCnpj), ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </td>
                                        <td class="px-4 py-3 text-end fw-medium text-dark">
                                            <?= number_format((float)($shareholder['percentage'] ?? 0), 2, ',', '.') ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($submission['message'])): ?>
            <div class="nd-card mb-4">
                <div class="nd-card-header">
                    <h2 class="nd-card-title">Mensagem Original</h2>
                </div>
                <div class="nd-card-body">
                    <div class="p-3 bg-light rounded text-secondary">
                        <?= nl2br(htmlspecialchars($submission['message'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <!-- Files sent by user -->
        <div class="nd-card mb-4">
            <div class="nd-card-header">
                <h2 class="nd-card-title">Documentos Anexados</h2>
            </div>
            <div class="nd-card-body p-0">
                <?php if (!$files): ?>
                    <div class="p-4 text-center text-muted small">
                        <i class="bi bi-paperclip fs-5 d-block mb-1 opacity-50"></i>
                        Nenhum anexo enviado.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php 
                        $docLabels = [
                            'BALANCE_SHEET'             => 'Último Balanço',
                            'DRE'                       => 'DRE',
                            'POLICIES'                  => 'Políticas',
                            'CNPJ_CARD'                 => 'Cartão CNPJ',
                            'POWER_OF_ATTORNEY'         => 'Procuração',
                            'MINUTES'                   => 'Ata',
                            'ARTICLES_OF_INCORPORATION' => 'Contrato Social',
                            'BYLAWS'                    => 'Estatuto',
                        ];
                        foreach ($files as $f): 
                            $label = $docLabels[$f['document_type'] ?? ''] ?? $f['original_name'];
                        ?>
                            <li class="list-group-item px-4 py-3 border-light-subtle">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-secondary">
                                        <i class="bi bi-file-earmark-text fs-4"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="text-truncate fw-medium text-dark" title="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <?php if (isset($f['size_bytes'])): ?>
                                            <div class="small text-muted">
                                                <?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Response files from admin -->
        <?php if ($responseFiles): ?>
            <div class="nd-card border-primary-subtle">
                <div class="nd-card-header bg-primary-subtle bg-opacity-10">
                    <h2 class="nd-card-title text-primary-emphasis">
                        <i class="bi bi-reply-all-fill me-2"></i>
                        Documentos de Retorno
                    </h2>
                </div>
                <div class="nd-card-body p-0">
                    <div class="p-3 bg-light border-bottom border-light-subtle">
                        <p class="small text-muted mb-0">
                            Estes são os documentos gerados para sua análise.
                        </p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($responseFiles as $f): ?>
                            <li class="list-group-item px-4 py-3 border-light-subtle">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center overflow-hidden me-2">
                                        <div class="me-3 text-primary">
                                            <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-truncate fw-medium text-dark" title="<?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB
                                            </div>
                                        </div>
                                    </div>
                                    <a href="/portal/files/<?= (int)$f['id'] ?>/download" 
                                       class="btn btn-sm btn-primary rounded-circle shadow-sm"
                                       title="Baixar arquivo">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>