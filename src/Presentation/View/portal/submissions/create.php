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

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 fw-bold text-dark mb-0"><?= __('submissions.new_submission') ?></h1>
    <a href="/portal/submissions" class="nd-btn nd-btn-sm nd-btn-outline"><?= __('actions.cancel') ?></a>
</div>

<?php if (isset($errors['general'])): ?>
    <div class="nd-alert nd-alert-danger mb-4" role="alert" aria-live="assertive">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <div class="nd-alert-text">
            <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
<?php endif; ?>

<form method="post" action="/portal/submissions" enctype="multipart/form-data" id="submissionForm">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="shareholders" id="shareholdersData">

    <!-- Informações da Empresa -->
    <div class="nd-card mb-4">
        <div class="nd-card-header">
            <h2 class="nd-card-title">Dados da Empresa</h2>
        </div>
        <div class="nd-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="nd-label" for="responsible_name">
                        Nome do Responsável <span class="text-danger" aria-hidden="true">*</span>
                        <span class="visually-hidden">obrigatório</span>
                    </label>
                    <input type="text" class="nd-input <?= isset($errors['responsible_name']) ? 'is-invalid' : '' ?>"
                        id="responsible_name" name="responsible_name" required
                        placeholder="Nome completo do responsável"
                        value="<?= htmlspecialchars($old['responsible_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        <?= isset($errors['responsible_name']) ? 'aria-invalid="true" aria-describedby="error_responsible_name"' : '' ?>>
                    <?php if (isset($errors['responsible_name'])): ?>
                        <div id="error_responsible_name" class="invalid-feedback"><?= htmlspecialchars($errors['responsible_name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="company_cnpj">
                        CNPJ <span class="text-danger" aria-hidden="true">*</span>
                        <span class="visually-hidden">obrigatório</span>
                    </label>
                    <div class="nd-input-group">
                        <input type="text" class="nd-input pe-5 <?= isset($errors['company_cnpj']) ? 'is-invalid' : '' ?>"
                            id="company_cnpj" name="company_cnpj" required
                            placeholder="00.000.000/0000-00"
                            value="<?= htmlspecialchars($old['company_cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            <?= isset($errors['company_cnpj']) ? 'aria-invalid="true" aria-describedby="error_company_cnpj"' : '' ?>>
                        <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-decoration-none pe-3" 
                                type="button" id="btnSearchCnpj" style="z-index: 5;" aria-label="Buscar dados do CNPJ">
                            <i class="bi bi-search text-primary" aria-hidden="true"></i> <span class="fw-medium">Preencher</span>
                        </button>
                    </div>
                    <?php if (isset($errors['company_cnpj'])): ?>
                        <div id="error_company_cnpj" class="invalid-feedback d-block"><?= htmlspecialchars($errors['company_cnpj'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <div class="form-text small text-muted mt-1">
                        <i class="bi bi-info-circle me-1"></i>
                        Clique em "Preencher" para buscar os dados automaticamente
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="company_name">
                        Nome da Empresa <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input nd-input-readonly <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>"
                        id="company_name" name="company_name" required readonly
                        placeholder="Razão Social"
                        value="<?= htmlspecialchars($old['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['company_name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['company_name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="main_activity">
                        Atividade Principal
                    </label>
                    <input type="text" class="nd-input nd-input-readonly" id="main_activity" name="main_activity" readonly
                        placeholder="CNAE ou descrição"
                        value="<?= htmlspecialchars($old['main_activity'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-4">
                    <label class="nd-label" for="phone">
                        Telefone <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                        id="phone" name="phone" required
                        placeholder="(00) 0000-0000"
                        value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-8">
                    <label class="nd-label" for="website">Site</label>
                    <input type="url" class="nd-input" id="website" name="website"
                        placeholder="https://www.exemplo.com.br"
                        value="<?= htmlspecialchars($old['website'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="net_worth">
                        Patrimônio Líquido <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input money <?= isset($errors['net_worth']) ? 'is-invalid' : '' ?>"
                        id="net_worth" name="net_worth" required
                        placeholder="R$ 0,00"
                        value="<?= htmlspecialchars($old['net_worth'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['net_worth'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['net_worth'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="annual_revenue">
                        Último Faturamento Anual <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input money <?= isset($errors['annual_revenue']) ? 'is-invalid' : '' ?>"
                        id="annual_revenue" name="annual_revenue" required
                        placeholder="R$ 0,00"
                        value="<?= htmlspecialchars($old['annual_revenue'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['annual_revenue'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['annual_revenue'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                </div>
            </div>
        </div>

    <!-- Declarações -->
    <div class="nd-card mb-4">
        <div class="nd-card-header">
            <h2 class="nd-card-title mb-0">Declarações <span class="text-danger">*</span></h2>
        </div>
        <div class="nd-card-body">
            <div class="d-flex gap-4">
                <div class="form-check nd-checkbox">
                    <input class="form-check-input" type="checkbox" id="is_us_person" name="is_us_person"
                        <?= !empty($old['is_us_person']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_us_person">
                        Sou US Person
                    </label>
                </div>
                <div class="form-check nd-checkbox">
                    <input class="form-check-input" type="checkbox" id="is_pep" name="is_pep"
                        <?= !empty($old['is_pep']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_pep">
                        Sou PEP (Pessoa Exposta Politicamente)
                    </label>
                </div>
                <div class="form-check nd-checkbox">
                    <input class="form-check-input" type="checkbox" id="is_none_compliant" name="is_none_compliant"
                        <?= !empty($old['is_none_compliant']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_none_compliant">
                        Não me enquadro nas opções
                    </label>
                </div>
            </div>
            <div id="complianceError" class="text-danger small mt-1" role="alert" style="<?= isset($errors['compliance']) ? 'display: block;' : 'display: none;' ?>">
                <?= $errors['compliance'] ?? 'Selecione pelo menos uma opção.' ?>
            </div>
        </div>
    </div>

    <!-- Composição Societária -->
    <div class="nd-card mb-4">
        <div class="nd-card-header d-flex justify-content-between align-items-center">
            <h2 class="nd-card-title mb-0">Composição Societária</h2>
            <button type="button" class="nd-btn nd-btn-sm nd-btn-outline" id="btnAddShareholder">
                <i class="bi bi-plus-lg me-1"></i> Incluir Sócio
            </button>
        </div>
        <div class="nd-card-body">
            <?php if (isset($errors['shareholders'])): ?>
                <div class="nd-alert nd-alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div class="nd-alert-text">
                        <?= htmlspecialchars($errors['shareholders'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            <?php endif; ?>

            <div id="shareholdersList" class="d-flex flex-column gap-3"></div>

            <div class="mt-4 p-3 bg-light rounded d-flex justify-content-between align-items-center border border-light-subtle">
                <span class="text-secondary fw-medium">Total da Participação</span>
                <div class="text-end">
                    <strong class="fs-5 text-dark"><span id="totalPercentage" aria-live="polite">0.00</span>%</strong>
                    <div id="percentageWarning" class="text-danger small mt-1" role="alert" style="display: none;">
                        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>A soma deve ser exatamente 100%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos Obrigatórios -->
    <div class="nd-card mb-4">
        <div class="nd-card-header">
            <h2 class="nd-card-title">Documentos Obrigatórios (PDF)</h2>
        </div>
        <div class="nd-card-body">
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
                    <label class="nd-label" for="<?= $name ?>">
                        <?= $label ?> <span class="text-danger">*</span>
                    </label>
                    <input type="file" class="nd-input form-control <?= isset($errors[$name]) ? 'is-invalid' : '' ?>"
                        id="<?= $name ?>" name="<?= $name ?>" accept=".pdf" required>
                    <?php if (isset($errors[$name])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors[$name], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Responsável pelo Cadastro -->
    <div class="nd-card mb-4">
        <div class="nd-card-header">
            <h2 class="nd-card-title">Seus Dados</h2>
        </div>
        <div class="nd-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="nd-label" for="registrant_name">
                        Nome Completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input <?= isset($errors['registrant_name']) ? 'is-invalid' : '' ?>"
                        id="registrant_name" name="registrant_name" required
                        placeholder="Seu nome completo"
                        value="<?= htmlspecialchars($old['registrant_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['registrant_name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['registrant_name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="registrant_position">Cargo</label>
                    <input type="text" class="nd-input" id="registrant_position" name="registrant_position"
                        placeholder="Seu cargo na empresa"
                        value="<?= htmlspecialchars($old['registrant_position'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="registrant_rg">RG</label>
                    <input type="text" class="nd-input" id="registrant_rg" name="registrant_rg"
                        placeholder="00.000.000-0"
                        value="<?= htmlspecialchars($old['registrant_rg'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="registrant_cpf">
                        CPF <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input <?= isset($errors['registrant_cpf']) ? 'is-invalid' : '' ?>"
                        id="registrant_cpf" name="registrant_cpf" required
                        placeholder="000.000.000-00"
                        value="<?= htmlspecialchars($old['registrant_cpf'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['registrant_cpf'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['registrant_cpf'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mb-5">
        <a href="/portal/submissions" class="nd-btn nd-btn-lg nd-btn-outline px-4">Cancelar</a>
        <button type="submit" class="nd-btn nd-btn-lg nd-btn-primary px-5">
            <i class="bi bi-send me-2"></i> Enviar
        </button>
    </div>
</form>

<script>
    window.SubmissionConfig = {
        shareholders: <?= json_encode($oldShareholders) ?>,
        csrfToken: "<?= $csrfToken ?>"
    };
</script>
<script src="/js/submission-create.js"></script>
