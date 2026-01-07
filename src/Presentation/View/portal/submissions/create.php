<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */

use App\Support\Csrf;
use App\Support\Session;

$oldShareholders = Session::getFlash('old_shareholders') ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 fw-bold text-dark mb-0">Novo Cadastro de Cliente</h1>
    <a href="/portal/submissions" class="nd-btn nd-btn-sm nd-btn-outline">Cancelar</a>
</div>

<?php if (isset($errors['general'])): ?>
    <div class="nd-alert nd-alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
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
            <h2 class="nd-card-title">Informações da Empresa</h2>
        </div>
        <div class="nd-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="nd-label" for="responsible_name">
                        Nome do Responsável <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="nd-input <?= isset($errors['responsible_name']) ? 'is-invalid' : '' ?>"
                        id="responsible_name" name="responsible_name" required
                        placeholder="Nome completo do responsável"
                        value="<?= htmlspecialchars($old['responsible_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['responsible_name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['responsible_name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="nd-label" for="company_cnpj">
                        CNPJ <span class="text-danger">*</span>
                    </label>
                    <div class="nd-input-group">
                        <input type="text" class="nd-input pe-5 <?= isset($errors['company_cnpj']) ? 'is-invalid' : '' ?>"
                            id="company_cnpj" name="company_cnpj" required
                            placeholder="00.000.000/0000-00"
                            value="<?= htmlspecialchars($old['company_cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-decoration-none pe-3" 
                                type="button" id="btnSearchCnpj" style="z-index: 5;">
                            <i class="bi bi-search text-primary"></i> <span class="fw-medium">Buscar</span>
                        </button>
                    </div>
                    <?php if (isset($errors['company_cnpj'])): ?>
                        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['company_cnpj'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <div class="form-text small text-muted mt-1">
                        <i class="bi bi-info-circle me-1"></i>
                        Clique em "Buscar" para preencher automaticamente
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
            <h2 class="nd-card-title mb-0">Declarações</h2>
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
                    <input class="form-check-input" type="checkbox" id="is_none_compliant">
                    <label class="form-check-label" for="is_none_compliant">
                        Não me enquadro nas opções
                    </label>
                </div>
            </div>
            <div id="complianceError" class="text-danger small mt-1" style="display: none;">
                Selecione pelo menos uma opção.
            </div>
        </div>
    </div>

    <!-- Composição Societária -->
    <div class="nd-card mb-4">
        <div class="nd-card-header d-flex justify-content-between align-items-center">
            <h2 class="nd-card-title mb-0">Composição Societária</h2>
            <button type="button" class="nd-btn nd-btn-sm nd-btn-outline" id="btnAddShareholder">
                <i class="bi bi-plus-lg me-1"></i> Adicionar Sócio
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
                    <strong class="fs-5 text-dark"><span id="totalPercentage">0.00</span>%</strong>
                    <div id="percentageWarning" class="text-danger small mt-1" style="display: none;">
                        <i class="bi bi-exclamation-circle me-1"></i>A soma deve ser exatamente 100%
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
                    'balance_sheet' => 'Último Balanço',
                    'dre' => 'DRE (Demonstração do Resultado do Exercício)',
                    'policies' => 'Políticas',
                    'cnpj_card' => 'Cartão CNPJ',
                    'power_of_attorney' => 'Procuração',
                    'minutes' => 'Ata',
                    'articles_of_incorporation' => 'Contrato Social',
                    'bylaws' => 'Estatuto'
                ];
                foreach ($docs as $field => $label): 
                ?>
                <div class="col-md-6">
                    <label class="nd-label" for="<?= $field ?>">
                        <?= $label ?> <span class="text-danger">*</span>
                    </label>
                    <input type="file" class="nd-input form-control <?= isset($errors[$field]) ? 'is-invalid' : '' ?>"
                        id="<?= $field ?>" name="<?= $field ?>" accept=".pdf" required>
                    <?php if (isset($errors[$field])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors[$field], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Responsável pelo Cadastro -->
    <div class="nd-card mb-4">
        <div class="nd-card-header">
            <h2 class="nd-card-title">Responsável pelo Cadastro</h2>
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
            <i class="bi bi-send me-2"></i> Enviar Cadastro
        </button>
    </div>
</form>

<script>
let shareholders = <?= json_encode($oldShareholders) ?>;

// Máscara para valores monetários
document.querySelectorAll('.money').forEach(input => {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (parseInt(value) / 100).toFixed(2);
        e.target.value = 'R$ ' + value.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    });
});

// Funções de Máscara reutilizáveis
function maskCnpj(input) {
    let value = input.value.replace(/\D/g, '');
    
    // Limita ao tamanho máximo de um CNPJ (14 dígitos)
    if (value.length > 14) value = value.slice(0, 14);
    
    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    input.value = value;
}

function maskRg(input) {
    let value = input.value.replace(/\D/g, '');
    
    // Limita (exemplo 9 dígitos)
    if (value.length > 9) value = value.slice(0, 9);

    value = value.replace(/(\d{2})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    input.value = value;
}

function maskCpf(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    input.value = value;
}

// Attach masks to static inputs
document.getElementById('company_cnpj').addEventListener('input', function(e) { maskCnpj(e.target); });
document.getElementById('registrant_cpf').addEventListener('input', function(e) { maskCpf(e.target); });
document.getElementById('registrant_rg')?.addEventListener('input', function(e) { maskRg(e.target); });


// Buscar CNPJ
document.getElementById('btnSearchCnpj').addEventListener('click', async function() {
    const cnpj = document.getElementById('company_cnpj').value;
    const btn = this;
    const originalContent = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    try {
        const formData = new FormData();
        formData.append('cnpj', cnpj);
        formData.append('_token', '<?= Csrf::token() ?>');
        
        const response = await fetch('/portal/api/cnpj', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.error) {
            alert(result.error);
        } else {
            document.getElementById('company_name').value = result.data.name || '';
            document.getElementById('main_activity').value = result.data.main_activity || '';
            document.getElementById('phone').value = result.data.phone || '';
        }
    } catch (error) {
        alert('Erro ao buscar CNPJ: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
});

// Gerenciar checkboxes de Compliance (US Person / PEP)
const usPerson = document.getElementById('is_us_person');
const pep = document.getElementById('is_pep');
const noneCompliant = document.getElementById('is_none_compliant');
const complianceError = document.getElementById('complianceError');

function updateComplianceChecks(e) {
    if (e.target === noneCompliant && noneCompliant.checked) {
        usPerson.checked = false;
        pep.checked = false;
    } else if ((e.target === usPerson || e.target === pep) && e.target.checked) {
        noneCompliant.checked = false;
    }
    
    // Hide error if any is checked
    if (usPerson.checked || pep.checked || noneCompliant.checked) {
        complianceError.style.display = 'none';
        usPerson.classList.remove('is-invalid');
        pep.classList.remove('is-invalid');
        noneCompliant.classList.remove('is-invalid');
    }
}

usPerson.addEventListener('change', updateComplianceChecks);
pep.addEventListener('change', updateComplianceChecks);
noneCompliant.addEventListener('change', updateComplianceChecks);

// Gerenciar sócios
function renderShareholders() {
    const container = document.getElementById('shareholdersList');
    container.innerHTML = '';
    
    let total = 0;
    
    shareholders.forEach((shareholder, index) => {
        total += parseFloat(shareholder.percentage || 0);
        
        const div = document.createElement('div');
        div.className = 'nd-card border-light-subtle shadow-none bg-light';
        div.innerHTML = `
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="nd-label small mb-1">Nome</label>
                        <input type="text" class="nd-input bg-white" value="${shareholder.name || ''}"
                            onchange="updateShareholder(${index}, 'name', this.value)" placeholder="Nome do Sócio">
                    </div>
                    <div class="col-md-2">
                        <label class="nd-label small mb-1">RG</label>
                        <input type="text" class="nd-input bg-white" value="${shareholder.rg || ''}"
                            oninput="maskRg(this)"
                            onchange="updateShareholder(${index}, 'rg', this.value)" placeholder="00.000.000-0">
                    </div>
                    <div class="col-md-3">
                        <label class="nd-label small mb-1">CNPJ</label>
                        <input type="text" class="nd-input bg-white" value="${shareholder.cnpj || ''}"
                            oninput="maskCnpj(this)"
                            onchange="updateShareholder(${index}, 'cnpj', this.value)" placeholder="00.000.000/0000-00">
                    </div>
                    <div class="col-md-2">
                        <label class="nd-label small mb-1">Porcentagem (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="nd-input form-control bg-white" value="${shareholder.percentage || ''}"
                                onchange="updateShareholder(${index}, 'percentage', this.value); renderShareholders();">
                            <span class="input-group-text bg-white border-start-0 text-muted">%</span>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="nd-label small mb-1 d-block">&nbsp;</label>
                        <div class="d-grid">
                            <button type="button" class="nd-btn nd-btn-sm nd-btn-danger-soft" onclick="removeShareholder(${index})" title="Remover Sócio" style="height: 48px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
    });
    
    document.getElementById('totalPercentage').textContent = total.toFixed(2);
    const warning = document.getElementById('percentageWarning');
    const totalEl = document.getElementById('totalPercentage');
    
    if (Math.abs(total - 100) > 0.01) {
        warning.style.display = 'block';
        totalEl.parentElement.classList.add('text-danger');
        totalEl.parentElement.classList.remove('text-success');
    } else {
        warning.style.display = 'none';
        totalEl.parentElement.classList.remove('text-danger');
        totalEl.parentElement.classList.add('text-success');
    }
    
    document.getElementById('shareholdersData').value = JSON.stringify(shareholders);
}

function addShareholder() {
    shareholders.push({ name: '', rg: '', cnpj: '', percentage: 0 });
    renderShareholders();
}

function removeShareholder(index) {
    shareholders.splice(index, 1);
    renderShareholders();
}

function updateShareholder(index, field, value) {
    shareholders[index][field] = value;
    document.getElementById('shareholdersData').value = JSON.stringify(shareholders);
}

document.getElementById('btnAddShareholder').addEventListener('click', addShareholder);

// Renderizar sócios iniciais
renderShareholders();

// Validação antes de enviar
document.getElementById('submissionForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Validar Compliance
    if (!usPerson.checked && !pep.checked && !noneCompliant.checked) {
        isValid = false;
        complianceError.style.display = 'block';
        usPerson.classList.add('is-invalid');
        pep.classList.add('is-invalid');
        noneCompliant.classList.add('is-invalid');
        // Visually scroll to it
        usPerson.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    const total = parseFloat(document.getElementById('totalPercentage').textContent);
    
    // Se não tiver sócios, libera (ou bloqueie se for obrigatório ter ao menos 1)
    if (shareholders.length > 0 && Math.abs(total - 100) > 0.01) {
        isValid = false;
        alert('A soma das porcentagens dos sócios deve ser exatamente 100%');
        const warning = document.getElementById('percentageWarning');
        warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    if (!isValid) e.preventDefault();
});
</script>
