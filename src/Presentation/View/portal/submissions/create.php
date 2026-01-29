<?php

/** 
 * @var string $csrfToken
 * @var array<string, string> $errors 
 * @var array<string, mixed> $old
 * @var array<int, array{name?: string, rg?: string, cnpj?: string, percentage?: float|string}> $oldShareholders
 */

use App\Support\Csrf;
use App\Support\Session;

$oldShareholders = Session::getFlash('old_shareholders') ?? [];
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-5">
    <h1 class="h3 fw-bold text-dark mb-0">Nova Solicitação</h1>
    <a href="/portal/submissions" class="btn btn-light bg-white border shadow-sm text-secondary hover-dark rounded-pill px-4">Cancelar</a>
</div>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4 rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
<?php endif; ?>

<form method="post" action="/portal/submissions" enctype="multipart/form-data" id="submissionForm">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="shareholders" id="shareholdersData">

    <!-- Stepper -->
    <div class="position-relative mb-5 mx-auto" style="max-width: 600px;">
        <div class="position-absolute top-50 start-0 w-100 translate-middle-y bg-light" style="height: 2px; z-index: 0;"></div>
        <!-- Progress Bar (Dynamic width based on active step) -->
        <div class="position-absolute top-50 start-0 translate-middle-y bg-warning transition-fast" style="height: 2px; w-0; z-index: 0;" id="stepperProgress"></div>
        
        <div class="d-flex justify-content-between position-relative" style="z-index: 1;">
            <!-- Step 1 -->
            <div class="nd-step-item text-center active" data-target="1">
                <div class="nd-step-box bg-white border border-2 border-warning text-warning fw-bold d-flex align-items-center justify-content-center shadow-sm mb-2 mx-auto transition-fast" 
                     style="width: 48px; height: 48px; font-size: 1.25rem;">1</div>
                <div class="nd-step-label small fw-bold text-dark transition-fast">Dados Iniciais</div>
            </div>
            <!-- Step 2 -->
            <div class="nd-step-item text-center" data-target="2">
                <div class="nd-step-box bg-white border border-2 border-light-subtle text-muted fw-bold d-flex align-items-center justify-content-center mb-2 mx-auto transition-fast" 
                     style="width: 48px; height: 48px; font-size: 1.25rem;">2</div>
                <div class="nd-step-label small text-muted transition-fast">Sócios</div>
            </div>
             <!-- Step 3 -->
            <div class="nd-step-item text-center" data-target="3">
                <div class="nd-step-box bg-white border border-2 border-light-subtle text-muted fw-bold d-flex align-items-center justify-content-center mb-2 mx-auto transition-fast" 
                     style="width: 48px; height: 48px; font-size: 1.25rem;">3</div>
                <div class="nd-step-label small text-muted transition-fast">Documentos</div>
            </div>
        </div>
    </div>

    <!-- STEP 1: Dados -->
    <div class="wizard-step active" data-step="1">
        
        <!-- Dados da Empresa -->
        <div class="nd-card mb-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="nd-card-header bg-white border-bottom p-4">
                <h5 class="nd-card-title fw-bold text-dark mb-0">Dados da Empresa</h5>
            </div>
            <div class="nd-card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Nome do Responsável <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 <?= isset($errors['responsible_name']) ? 'is-invalid' : '' ?>"
                            id="responsible_name" name="responsible_name" required
                            placeholder="Nome completo do responsável"
                            value="<?= htmlspecialchars($old['responsible_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['responsible_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['responsible_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">CNPJ <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 pe-5 <?= isset($errors['company_cnpj']) ? 'is-invalid' : '' ?>"
                                id="company_cnpj" name="company_cnpj" required
                                placeholder="00.000.000/0000-00"
                                value="<?= htmlspecialchars($old['company_cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-decoration-none pe-3 fw-bold small" 
                                    type="button" id="btnSearchCnpj" style="z-index: 5;">
                                <i class="bi bi-search me-1"></i> Preencher
                            </button>
                        </div>
                         <?php if (isset($errors['company_cnpj'])): ?>
                            <div class="d-block invalid-feedback mt-1"><?= htmlspecialchars($errors['company_cnpj'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Nome da Empresa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 text-muted <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>"
                            id="company_name" name="company_name" required readonly
                            placeholder="Razão Social"
                            value="<?= htmlspecialchars($old['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Atividade Principal</label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 text-muted" id="main_activity" name="main_activity" readonly
                            placeholder="CNAE ou descrição"
                            value="<?= htmlspecialchars($old['main_activity'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Telefone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                            id="phone" name="phone" required
                            placeholder="(00) 0000-0000"
                            value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Site</label>
                        <input type="url" class="form-control bg-light border-0 py-3 px-3 rounded-3" id="website" name="website"
                            placeholder="https://www.exemplo.com.br"
                            value="<?= htmlspecialchars($old['website'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                     <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Patrimônio Líquido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 money <?= isset($errors['net_worth']) ? 'is-invalid' : '' ?>"
                            id="net_worth" name="net_worth" required
                            placeholder="R$ 0,00"
                            value="<?= htmlspecialchars($old['net_worth'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Último Faturamento Anual <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 money <?= isset($errors['annual_revenue']) ? 'is-invalid' : '' ?>"
                            id="annual_revenue" name="annual_revenue" required
                            placeholder="R$ 0,00"
                            value="<?= htmlspecialchars($old['annual_revenue'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Seus Dados -->
        <div class="nd-card mb-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="nd-card-header bg-white border-bottom p-4">
                <h5 class="nd-card-title fw-bold text-dark mb-0">Seus Dados</h5>
            </div>
            <div class="nd-card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 <?= isset($errors['registrant_name']) ? 'is-invalid' : '' ?>"
                            id="registrant_name" name="registrant_name" required
                            placeholder="Seu nome completo"
                            value="<?= htmlspecialchars($old['registrant_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Cargo</label>
                        <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3" id="registrant_position" name="registrant_position"
                            placeholder="Seu cargo na empresa"
                            value="<?= htmlspecialchars($old['registrant_position'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-6">
                         <label class="form-label small fw-bold text-secondary text-uppercase ls-1">RG</label>
                         <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3" id="registrant_rg" name="registrant_rg"
                            placeholder="00.000.000-0"
                            value="<?= htmlspecialchars($old['registrant_rg'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-6">
                         <label class="form-label small fw-bold text-secondary text-uppercase ls-1">CPF <span class="text-danger">*</span></label>
                         <input type="text" class="form-control bg-light border-0 py-3 px-3 rounded-3 <?= isset($errors['registrant_cpf']) ? 'is-invalid' : '' ?>"
                            id="registrant_cpf" name="registrant_cpf" required
                            placeholder="000.000.000-00"
                            value="<?= htmlspecialchars($old['registrant_cpf'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Declarações -->
        <div class="nd-card mb-5 border-0 shadow-sm rounded-4 bg-white">
            <div class="nd-card-header bg-white border-bottom p-4">
                <h5 class="nd-card-title fw-bold text-dark mb-0">Declarações <span class="text-danger">*</span></h5>
            </div>
            <div class="nd-card-body p-4">
                 <div class="d-flex gap-4 flex-wrap">
                    <div class="form-check nd-checkbox">
                        <input class="form-check-input" type="checkbox" id="is_us_person" name="is_us_person"
                            <?= !empty($old['is_us_person']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-secondary fw-medium" for="is_us_person">
                            Sou US Person
                        </label>
                    </div>
                    <div class="form-check nd-checkbox">
                        <input class="form-check-input" type="checkbox" id="is_pep" name="is_pep"
                            <?= !empty($old['is_pep']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-secondary fw-medium" for="is_pep">
                            Sou PEP (Pessoa Exposta Politicamente)
                        </label>
                    </div>
                    <div class="form-check nd-checkbox">
                        <input class="form-check-input" type="checkbox" id="is_none_compliant" name="is_none_compliant"
                            <?= !empty($old['is_none_compliant']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-secondary fw-medium" for="is_none_compliant">
                            Não me enquadro nas opções
                        </label>
                    </div>
                </div>
                 <div id="complianceError" class="text-danger small mt-2 fw-bold" role="alert" style="<?= isset($errors['compliance']) ? 'display: block;' : 'display: none;' ?>">
                    <i class="bi bi-exclamation-circle me-1"></i> <?= $errors['compliance'] ?? 'Selecione pelo menos uma opção.' ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-5">
            <button type="button" class="nd-btn nd-btn-gold shadow px-5 py-3 rounded-pill btn-next hover-scale" data-next="2">
                <span class="fw-bold text-uppercase ls-1">Próximo Passo</span> <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    <!-- STEP 2: Socios -->
    <div class="wizard-step" data-step="2">
         <div class="nd-card mb-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="nd-card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="nd-card-title fw-bold text-dark mb-0">Composição Societária</h5>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddShareholder">
                    <i class="bi bi-plus-lg me-1"></i> Incluir Sócio
                </button>
            </div>
            <div class="nd-card-body p-4">
                 <div id="shareholdersList" class="d-flex flex-column gap-3"></div>

                 <div class="mt-4 p-4 bg-light rounded-4 border border-light-subtle d-flex justify-content-between align-items-center">
                    <span class="text-secondary fw-bold text-uppercase ls-1 small">Total da Participação</span>
                    <div class="text-end">
                        <div class="display-6 fw-bold text-dark me-2"><span id="totalPercentage">0.00</span><span class="fs-4">%</span></div>
                        <div id="percentageWarning" class="text-danger x-small fw-bold mt-1" style="display: none;">
                            <i class="bi bi-exclamation-circle me-1"></i>A soma deve ser 100%
                        </div>
                    </div>
                </div>
            </div>
         </div>

        <div class="d-flex justify-content-between mb-5">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-3 btn-prev fw-bold text-uppercase ls-1" data-prev="1">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </button>
            <button type="button" class="nd-btn nd-btn-gold shadow px-5 py-3 rounded-pill btn-next hover-scale" data-next="3">
                <span class="fw-bold text-uppercase ls-1">Próximo Passo</span> <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    <!-- STEP 3: Documentos -->
    <div class="wizard-step" data-step="3">
         <div class="nd-card mb-4 border-0 shadow-sm rounded-4 bg-white">
            <div class="nd-card-header bg-white border-bottom p-4">
                <h5 class="nd-card-title fw-bold text-dark mb-0">Documentos Obrigatórios (PDF)</h5>
            </div>
            <div class="nd-card-body p-4">
                <div class="row g-4">
                     <?php 
                    $docs = [
                        'ultimo_balanco' => 'Último Balanço',
                        'dre' => 'DRE (Demonstração do Resultado do Exercício)',
                        'politicas' => 'Políticas',
                        'cartao_cnpj' => 'Cartão CNPJ',
                        'procuracao' => 'Procuração',
                        'ata' => 'Ata',
                        'contrato_social' => 'Contrato Social',
                        'estatuto' => 'Estatuto',
                    ];
                    foreach ($docs as $name => $label): 
                    ?>
                    <div class="col-md-6">
                         <label class="form-label small fw-bold text-secondary text-uppercase ls-1 mb-2">
                            <?= $label ?> <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control bg-light border-0 py-3 px-3 rounded-3 <?= isset($errors[$name]) ? 'is-invalid' : '' ?>"
                            id="<?= $name ?>" name="<?= $name ?>" accept=".pdf" required>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
         </div>

        <div class="d-flex justify-content-between mb-5">
             <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-3 btn-prev fw-bold text-uppercase ls-1" data-prev="2">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </button>
            <div class="d-flex gap-3">
                 <a href="/portal/submissions" class="btn btn-light bg-white border text-muted shadow-sm rounded-pill px-4 py-3 fw-bold text-uppercase ls-1">Cancelar</a>
                <button type="submit" class="nd-btn nd-btn-gold shadow px-5 py-3 rounded-pill btn-next hover-scale">
                    <i class="bi bi-send me-2"></i> <span class="fw-bold text-uppercase ls-1">Enviar Solicitação</span>
                </button>
            </div>
        </div>
    </div>

</form>

<script>
    window.SubmissionConfig = {
        shareholders: <?= json_encode($oldShareholders) ?>,
        csrfToken: "<?= $csrfToken ?>"
    };
</script>
<script src="/js/submission-create.js"></script>

