<?php

/** @var array $submission */
/** @var array $userFiles */
/** @var array $responseFiles */
/** @var array $notes */
/** @var string $csrfToken */

$statusClass = match($submission['status'] ?? '') {
    'PENDING' => 'warning',
    'UNDER_REVIEW' => 'info',
    'NEEDS_CORRECTION' => 'gold',
    'COMPLETED', 'APPROVED' => 'success',
    'REJECTED' => 'danger',
    default => 'secondary'
};

$statusLabel = match($submission['status'] ?? '') {
    'PENDING' => 'Pendente',
    'UNDER_REVIEW' => 'Em Análise',
    'NEEDS_CORRECTION' => 'Precisa de Correção',
    'COMPLETED' => 'Concluído',
    'APPROVED' => 'Aprovado',
    'REJECTED' => 'Rejeitado',
    default => $submission['status'] ?? '-'
};

$statusIcon = match($submission['status'] ?? '') {
    'PENDING' => 'bi-hourglass-split',
    'UNDER_REVIEW' => 'bi-search',
    'NEEDS_CORRECTION' => 'bi-exclamation-triangle-fill',
    'COMPLETED', 'APPROVED' => 'bi-check-circle-fill',
    'REJECTED' => 'bi-x-circle-fill',
    default => 'bi-circle'
};
?>

<div class="d-flex flex-column gap-4">

    <!-- Header com Botão Voltar e Ações Rápidas -->
    <div class="d-flex align-items-center justify-content-between">
        <a href="/admin/submissions" class="nd-btn nd-btn-outline nd-btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar para Lista
        </a>
        <div class="d-flex gap-2">
            <form method="post" action="/admin/submissions/<?= (int)$submission['id'] ?>/resend-notification" class="d-inline">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="nd-btn nd-btn-outline nd-btn-sm" 
                    onclick="return confirm('Reenviar notificação para o usuário?');">
                    <i class="bi bi-envelope me-1"></i> Reenviar Notificação
                </button>
            </form>
            <a href="/admin/submissions/export/print?id=<?= (int)$submission['id'] ?>" target="_blank" class="nd-btn nd-btn-ghost nd-btn-sm" title="Imprimir">
                <i class="bi bi-printer"></i>
            </a>
        </div>
    </div>

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

            <?php if (!in_array($submission['status'] ?? '', ['COMPLETED', 'APPROVED', 'REJECTED'])): ?>
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
                                <option value="NEEDS_CORRECTION" <?= ($submission['status'] ?? '') === 'NEEDS_CORRECTION' ? 'selected' : '' ?>>
                                    ⚠️ Aguardando Correção
                                </option>
                                <option value="APPROVED" <?= in_array($submission['status'] ?? '', ['COMPLETED', 'APPROVED']) ? 'selected' : '' ?>>
                                    ✅ Aprovado/Concluído
                                </option>
                                <option value="REJECTED" <?= ($submission['status'] ?? '') === 'REJECTED' ? 'selected' : '' ?>>
                                    ❌ Rejeitado
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="nd-label" for="visibility">Visibilidade da Observação</label>
                            <select class="nd-input" id="visibility" name="visibility">
                                <option value="USER_VISIBLE">👁️ Visível ao solicitante no Portal</option>
                                <option value="ADMIN_ONLY">🔒 Apenas interna administradores</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="nd-label" for="note">Observação / Comentário (opcional)</label>
                            <textarea class="nd-input" id="note" name="note" rows="3" 
                                placeholder="Adicione um comentário ou detalhe o que precisa ser corrigido pelo usuário..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="nd-btn nd-btn-primary" onclick="document.getElementById('status').value='APPROVED'; return confirm('✅ Tem certeza que deseja APROVAR este envio?\n\nEsta ação notificará o solicitante.');">
                                    <i class="bi bi-check-lg me-1"></i> Aprovar
                                </button>
                                <button type="submit" class="nd-btn nd-btn-outline" style="border-color: var(--nd-gold-600); color: var(--nd-gold-600);" onclick="document.getElementById('status').value='NEEDS_CORRECTION'; if(!document.getElementById('note').value) { alert('Você precisa escrever uma observação detalhando o que deve ser corrigido pelo usuário.'); return false; } return confirm('⚠️ Devolver para Correção?\n\nO solicitante receberá o comentário acima e poderá alterar a submissão.');">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Solicitar Correção
                                </button>
                                <button type="submit" class="nd-btn nd-btn-outline" style="border-color: var(--nd-danger); color: var(--nd-danger);" 
                                    onclick="document.getElementById('status').value='REJECTED'; return confirm('⚠️ Tem certeza que deseja REJEITAR este envio?\n\nEsta ação notificará o solicitante e encerrará a submissão.');">
                                    <i class="bi bi-x-lg me-1"></i> Rejeitar
                                </button>
                                <button type="submit" class="nd-btn nd-btn-gold ms-auto">
                                    <i class="bi bi-send me-1"></i> Enviar Comentário Interno
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informações Complementares -->
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color: var(--nd-navy-500);"></i>
            <h2 class="nd-card-title mb-0">Informações Complementares</h2>
        </div>
        <div class="nd-card-body">
            <div class="row g-4">
                <!-- Dados da Empresa -->
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">Dados da Empresa</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="row">
                            <label class="col-sm-4 text-muted small">Razão Social</label>
                            <div class="col-sm-8 fw-medium"><?= htmlspecialchars($submission['company_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 text-muted small">CNPJ</label>
                            <div class="col-sm-8 fw-medium">
                                <?php
                                    $cnpj = preg_replace('/\D/', '', $submission['company_cnpj'] ?? '');
                                    echo mb_strlen($cnpj) === 14 
                                        ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj)
                                        : htmlspecialchars($submission['company_cnpj'] ?? '-', ENT_QUOTES, 'UTF-8');
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 text-muted small">Atividade Principal</label>
                            <div class="col-sm-8"><?= htmlspecialchars($submission['main_activity'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 text-muted small">Telefone</label>
                            <div class="col-sm-8"><?= htmlspecialchars($submission['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 text-muted small">Website</label>
                            <div class="col-sm-8">
                                <?php if (!empty($submission['website'])): ?>
                                    <a href="<?= htmlspecialchars($submission['website'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-primary text-decoration-none">
                                        <?= htmlspecialchars($submission['website'], ENT_QUOTES, 'UTF-8') ?> <i class="bi bi-box-arrow-up-right small ms-1"></i>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dados Financeiros & Compliance -->
                <div class="col-md-6">
                     <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">Financeiro & Compliance</h6>
                     <div class="d-flex flex-column gap-2">
                        <div class="row">
                            <label class="col-sm-5 text-muted small">Patrimônio Líquido</label>
                            <div class="col-sm-7 fw-medium">
                                <?= !empty($submission['net_worth']) ? 'R$ ' . number_format((float)$submission['net_worth'], 2, ',', '.') : '-' ?>
                            </div>
                        </div>
                         <div class="row">
                            <label class="col-sm-5 text-muted small">Faturamento Anual</label>
                            <div class="col-sm-7 fw-medium">
                                <?= !empty($submission['annual_revenue']) ? 'R$ ' . number_format((float)$submission['annual_revenue'], 2, ',', '.') : '-' ?>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <label class="col-sm-5 text-muted small">US Person?</label>
                            <div class="col-sm-7">
                                <?= !empty($submission['is_us_person']) ? '<span class="text-danger fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Sim</span>' : '<span class="text-secondary"><i class="bi bi-x-circle me-1"></i> Não</span>' ?>
                            </div>
                        </div>
                         <div class="row">
                            <label class="col-sm-5 text-muted small">Pessoa Exposta (PEP)?</label>
                            <div class="col-sm-7">
                                 <?= !empty($submission['is_pep']) ? '<span class="text-danger fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Sim</span>' : '<span class="text-secondary"><i class="bi bi-x-circle me-1"></i> Não</span>' ?>
                            </div>
                        </div>
                     </div>
                </div>

                <!-- Dados do Declarante -->
                <div class="col-12">
                     <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">Dados do Declarante</h6>
                     <div class="row g-3">
                        <div class="col-md-3">
                            <label class="text-muted small d-block">Nome do Declarante</label>
                            <span class="fw-medium"><?= htmlspecialchars($submission['registrant_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                         <div class="col-md-3">
                            <label class="text-muted small d-block">Cargo / Função</label>
                            <span class="fw-medium"><?= htmlspecialchars($submission['registrant_position'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                         <div class="col-md-3">
                            <label class="text-muted small d-block">CPF do Declarante</label>
                            <span class="fw-medium">
                                <?php 
                                    $cpf = preg_replace('/\D/', '', $submission['registrant_cpf'] ?? '');
                                    echo mb_strlen($cpf) === 11 
                                        ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf) 
                                        : htmlspecialchars($submission['registrant_cpf'] ?? '-', ENT_QUOTES, 'UTF-8');
                                ?>
                            </span>
                        </div>
                         <div class="col-md-3">
                            <label class="text-muted small d-block">RG do Declarante</label>
                            <span class="fw-medium"><?= htmlspecialchars($submission['registrant_rg'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
    // Mix comments and status history into a single timeline
    // This allows us to see "Admin changed status" intertwined with "Admin said: Please fix this"
    $timeline = [];
    $allNotes = $notes ?? [];
    $allComments = $comments ?? [];
    $allHistory = $statusHistory ?? [];
    
    // Legacy Notes mapping
    foreach ($allNotes as $note) {
        $timeline[] = [
            'type' => 'note',
            'created_at' => $note['created_at'],
            'data' => $note
        ];
    }
    
    // New Comments mapping
    foreach ($allComments as $comment) {
        $timeline[] = [
            'type' => 'comment',
            'created_at' => $comment['created_at'],
            'data' => $comment
        ];
    }
    
    // Status History mapping
    foreach ($allHistory as $history) {
        // Skip initial PENDING as it's just creation noise usually, unless it has a specific reason
        if (($history['old_status'] ?? '') === '' && $history['new_status'] === 'PENDING') {
            continue;
        }
        $timeline[] = [
            'type' => 'status',
            'created_at' => $history['created_at'],
            'data' => $history
        ];
    }
    
    // Sort timeline chronologically
    usort($timeline, function($a, $b) {
        return strtotime($a['created_at']) <=> strtotime($b['created_at']);
    });
    
    if (!empty($timeline)): 
    ?>
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-chat-left-text" style="color: var(--nd-info);"></i>
            <h2 class="nd-card-title mb-0">Timeline da Submissão</h2>
            <span class="nd-badge nd-badge-secondary ms-2"><?= count($timeline) ?> interações</span>
        </div>
        <div class="nd-card-body pb-0">
            <div class="timeline-container position-relative mb-4" style="padding-left: 20px;">
                <div class="position-absolute h-100" style="left: 6px; top: 10px; width: 2px; background: var(--nd-gray-200);"></div>
                
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($timeline as $item): 
                        $createdObj = new DateTime($item['created_at']);
                        $formattedDate = $createdObj->format('d/m/Y H:i');
                        
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
                            $badgeClass = match($history['new_status']) {
                                'PENDING' => 'warning',
                                'UNDER_REVIEW' => 'info',
                                'NEEDS_CORRECTION' => 'gold',
                                'COMPLETED', 'APPROVED' => 'success',
                                'REJECTED' => 'danger',
                                default => 'secondary'
                            };
                            $actorName = htmlspecialchars($history['changed_by_name'] ?? 'Sistema', ENT_QUOTES, 'UTF-8');
                            
                            echo "<div class='position-relative' style='padding-left: 30px;'>";
                            echo "<div class='position-absolute rounded-circle border border-white border-2 bg-white' style='left: -20px; top: 0px;'>";
                            echo "<i class='bi bi-arrow-repeat text-muted' style='font-size: 1.1rem;'></i></div>";
                            echo "<div class='small text-muted mb-1'>$formattedDate</div>";
                            echo "<div><strong>{$actorName}</strong> alterou o status para <span class='nd-badge nd-badge-{$badgeClass}'>{$newStatusLabel}</span></div>";
                            echo "</div>";
                            
                        } elseif ($item['type'] === 'note' || $item['type'] === 'comment') {
                            $isLegacy = ($item['type'] === 'note');
                            $data = $item['data'];
                            
                            $isInternal = $isLegacy ? ($data['visibility'] === 'ADMIN_ONLY') : (bool)$data['is_internal'];
                            $requiresAction = !$isLegacy && (bool)$data['requires_action'];
                            
                            if ($isLegacy) {
                                $authorName = htmlspecialchars($data['admin_name'] ?? 'Administrador', ENT_QUOTES, 'UTF-8');
                                $authorInitial = strtoupper(substr($data['admin_name'] ?? 'A', 0, 1));
                                $avatarClass = 'bg-primary text-white';
                            } else {
                                $authorName = htmlspecialchars($data['author_name'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');
                                $authorInitial = strtoupper(substr($authorName, 0, 1));
                                $isAdmin = ($data['author_type'] === 'ADMIN');
                                $avatarClass = $isAdmin ? 'bg-primary text-white' : 'bg-secondary text-white';
                            }
                            
                            $boxClass = $isInternal ? 'border-start border-3 border-warning' : 'border';
                            $boxBg = $isInternal ? 'style="background: #fffdf5;"' : 'style="background: #fff;"';
                    ?>
                        <div class="position-relative" style="padding-left: 30px;">
                            <div class="position-absolute" style="left: -25px; top: 0px;">
                                <div class="nd-avatar <?= $avatarClass ?> shadow-sm" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                    <?= $authorInitial ?>
                                </div>
                            </div>
                            
                            <div class="p-3 rounded shadow-sm <?= $boxClass ?>" <?= $boxBg ?>>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold"><?= $authorName ?></span>
                                        <?php if ($isInternal): ?>
                                            <span class="nd-badge nd-badge-warning" style="font-size: 0.65rem;"><i class="bi bi-lock me-1"></i>Interno</span>
                                        <?php endif; ?>
                                        <?php if ($requiresAction): ?>
                                            <span class="nd-badge nd-badge-danger" style="font-size: 0.65rem;"><i class="bi bi-exclamation-triangle me-1"></i>Ação Necessária</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i><?= $formattedDate ?></small>
                                </div>
                                <div class="text-secondary" style="font-size: 0.95rem; white-space: pre-wrap;"><?= htmlspecialchars($data['comment'] ?? $data['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    <?php 
                        }
                    endforeach; 
                    ?>
                </div>
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
                <div class="row g-3">
                    <?php foreach ($userFiles as $f): 
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
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded h-100" style="background: var(--nd-gray-50); border: 1px solid var(--nd-gray-200);">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="nd-avatar flex-shrink-0" style="width: 40px; height: 40px; background: rgba(26, 41, 66, 0.1); color: var(--nd-navy-700);">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-medium text-dark text-truncate" title="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="text-muted small text-uppercase" style="font-size: 0.75rem;">
                                            <?= strtoupper($ext) ?> &bull; <?= number_format((int)$f['size_bytes'] / 1024, 1, ',', '.') ?> KB
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 ms-2 flex-shrink-0">
                                    <?php
                                    $previewableMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                                    $mime = $f['mime_type'] ?? '';
                                    if (in_array($mime, $previewableMimes, true)):
                                    ?>
                                        <a href="/admin/files/<?= (int)$f['id'] ?>/preview" target="_blank" class="nd-btn nd-btn-ghost nd-btn-sm" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="/admin/files/<?= (int)$f['id'] ?>/download" class="nd-btn nd-btn-outline nd-btn-sm" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
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
                        <i class="bi bi-send me-2"></i>Enviar Arquivos
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Trilha de Auditoria -->
    <?php if (!empty($auditLogs)): ?>
    <div class="nd-card">
        <div class="nd-card-header d-flex align-items-center gap-2">
            <i class="bi bi-shield-check" style="color: var(--nd-navy-500);"></i>
            <h2 class="nd-card-title mb-0">Trilha de Auditoria</h2>
            <span class="nd-badge nd-badge-secondary ms-2"><?= count($auditLogs) ?></span>
        </div>
        <div class="nd-card-body p-0">
            <div class="nd-table-wrapper" style="border: none; border-radius: 0;">
                <table class="nd-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Data/Hora</th>
                            <th style="width: 180px;">Ação</th>
                            <th>Usuário</th>
                            <th>Detalhes</th>
                            <th style="width: 120px;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditLogs as $log): ?>
                            <?php
                            $actionLabels = [
                                'SUBMISSION_CREATED' => ['label' => 'Submissão Criada', 'icon' => 'bi-plus-circle', 'class' => 'success'],
                                'PORTAL_SUBMISSION_CREATED' => ['label' => 'Enviado pelo Portal', 'icon' => 'bi-cloud-upload', 'class' => 'primary'],
                                'SUBMISSION_STATUS_CHANGED' => ['label' => 'Status Alterado', 'icon' => 'bi-arrow-repeat', 'class' => 'info'],
                                'SUBMISSION_NOTIFICATION_RESENT' => ['label' => 'Notificação Reenviada', 'icon' => 'bi-envelope', 'class' => 'warning'],
                                'SUBMISSION_RESPONSE_FILES_UPLOADED' => ['label' => 'Resposta Enviada', 'icon' => 'bi-file-earmark-check', 'class' => 'gold'],
                                'VIRUS_DETECTED' => ['label' => 'Vírus Detectado', 'icon' => 'bi-bug', 'class' => 'danger'],
                            ];
                            $action = $log['action'] ?? '';
                            $config = $actionLabels[$action] ?? ['label' => ucwords(str_replace('_', ' ', strtolower($action))), 'icon' => 'bi-circle', 'class' => 'secondary'];
                            
                            $details = [];
                            if (!empty($log['details'])) {
                                $parsed = json_decode($log['details'], true);
                                if (is_array($parsed)) {
                                    foreach ($parsed as $k => $v) {
                                        if (is_string($v)) {
                                            $details[] = ucfirst(str_replace('_', ' ', $k)) . ': ' . $v;
                                        }
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <td class="text-muted small">
                                    <?php
                                    $dt = $log['occurred_at'] ?? '';
                                    if ($dt) {
                                        try {
                                            $d = new DateTime($dt);
                                            echo $d->format('d/m/Y H:i');
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($dt, ENT_QUOTES, 'UTF-8');
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="nd-badge nd-badge-<?= $config['class'] ?>">
                                        <i class="bi <?= $config['icon'] ?> me-1"></i>
                                        <?= htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="nd-avatar" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                            <?= strtoupper(substr($log['actor_name'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <span class="small"><?= htmlspecialchars($log['actor_name'] ?? 'Sistema', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars(implode(' | ', $details) ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="text-muted small font-monospace">
                                    <?= htmlspecialchars($log['ip_address'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>