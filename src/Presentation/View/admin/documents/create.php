<?php
/**
 * Espera:
 * - array $users (id, full_name, email)
 * - string $csrfToken
 */
$users = $users ?? [];
?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/documents" class="text-decoration-none">
            <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
                <i class="bi bi-cloud-arrow-up-fill text-white"></i>
            </div>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);">Enviar Novo Documento</h1>
            <p class="text-muted mb-0 small">Disponibilizar arquivo no portal do cliente</p>
        </div>
    </div>
    <a href="/admin/documents" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-list-ul me-1"></i> Visualizar Listagem
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="nd-card mb-4">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Dados do Documento</h5>
            </div>
            
            <div class="nd-card-body">
                <form action="/admin/documents" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- User Selection -->
                    <div class="mb-4">
                        <label class="nd-label">Cliente Destinatário <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <select name="portal_user_id" class="nd-input form-select" required style="padding-left: 2.5rem;">
                                <option value="">Selecione quem receberá este documento...</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>">
                                        <?= htmlspecialchars($u['full_name'] ?? $u['email'] ?? ('#' . (int)$u['id']), ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="bi bi-person nd-input-icon"></i>
                        </div>
                        <div class="form-text small">O cliente receberá uma notificação sobre este novo documento.</div>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label class="nd-label">Título do Documento <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text" name="title" class="nd-input" required placeholder="Ex: Contrato de Prestação de Serviços 2024" style="padding-left: 2.5rem;">
                            <i class="bi bi-card-heading nd-input-icon"></i>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                         <label class="nd-label">Descrição / Observações</label>
                        <textarea name="description" class="nd-input w-100" rows="4" placeholder="Adicione detalhes sobre o arquivo, se necessário..." style="resize: none;"></textarea>
                    </div>

                    <!-- File -->
                    <div class="mb-4">
                        <label class="nd-label">Arquivo para Upload <span class="text-danger">*</span></label>
                        <div class="nd-input p-2 pb-1">
                             <input type="file" name="file" class="form-control border-0 bg-transparent p-0" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                        </div>
                        <div class="form-text small mt-2">
                             <i class="bi bi-info-circle me-1"></i> 
                             Formatos aceitos: PDF, Word, Excel, Imagens e ZIP. Tamanho máximo recomendado: 50MB.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="/admin/documents" class="nd-btn nd-btn-outline">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-send-fill me-1"></i> Enviar e Notificar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="nd-card bg-light border-0 mb-4">
            <div class="nd-card-body">
                <h6 class="nd-card-title mb-3 d-flex align-items-center gap-2 text-dark">
                    <i class="bi bi-question-circle-fill text-primary"></i> Como funciona?
                </h6>
                <p class="small text-muted mb-3">
                    Essa ferramenta disponibiliza arquivos diretamente na área segura do cliente no portal.
                </p>
                <div class="d-flex flex-column gap-3 small">
                    <div class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <div>
                            <strong class="text-dark">Acesso Imediato</strong>
                            <div class="text-muted">Assim que enviado, o cliente poderá visualizar e baixar o arquivo.</div>
                        </div>
                    </div>
                     <div class="d-flex gap-2">
                        <i class="bi bi-shield-lock-fill text-primary mt-1"></i>
                        <div>
                            <strong class="text-dark">Segurança e Auditoria</strong>
                            <div class="text-muted">O upload fica registrado no histórico do cliente para fins de conformidade.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="nd-card bg-white border border-warning-subtle">
             <div class="nd-card-body">
                <h6 class="fw-bold text-warning-emphasis mb-2">
                    <i class="bi bi-lightbulb-fill me-1"></i> Dica
                </h6>
                <p class="small text-muted mb-0">
                    Utilize títulos claros e objetivos (ex: "Balanço 2023", "Nota Fiscal 123") para facilitar a busca pelo cliente.
                </p>
             </div>
        </div>
    </div>
</div>
