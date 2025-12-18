<?php

/** @var string $csrfToken */
/** @var array $errors */
/** @var array $old */

use App\Support\Csrf;
use App\Support\Session;

$oldShareholders = Session::getFlash('old_shareholders') ?? [];
?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <h1 class="h4 mb-3">Novo Cadastro de Cliente</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/portal/submissions" enctype="multipart/form-data" id="submissionForm">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="shareholders" id="shareholdersData">

            <!-- Informações da Empresa -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informações da Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="responsible_name">
                                Nome do Responsável <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control <?= isset($errors['responsible_name']) ? 'is-invalid' : '' ?>"
                                id="responsible_name" name="responsible_name" required
                                value="<?= htmlspecialchars($old['responsible_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['responsible_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['responsible_name'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="company_cnpj">
                                CNPJ <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control <?= isset($errors['company_cnpj']) ? 'is-invalid' : '' ?>"
                                    id="company_cnpj" name="company_cnpj" required
                                    placeholder="00.000.000/0000-00"
                                    value="<?= htmlspecialchars($old['company_cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button class="btn btn-outline-secondary" type="button" id="btnSearchCnpj">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                            <?php if (isset($errors['company_cnpj'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['company_cnpj'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Clique em "Buscar" para preencher automaticamente os dados da empresa</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="company_name">
                                Nome da Empresa <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>"
                                id="company_name" name="company_name" required readonly
                                value="<?= htmlspecialchars($old['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['company_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['company_name'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="main_activity">
                                Atividade Principal
                            </label>
                            <input type="text" class="form-control" id="main_activity" name="main_activity" readonly
                                value="<?= htmlspecialchars($old['main_activity'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="phone">
                                Telefone <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                id="phone" name="phone" required
                                placeholder="(00) 0000-0000"
                                value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="website">Site</label>
                            <input type="url" class="form-control" id="website" name="website"
                                placeholder="https://www.exemplo.com.br"
                                value="<?= htmlspecialchars($old['website'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="net_worth">
                                Patrimônio Líquido <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control money <?= isset($errors['net_worth']) ? 'is-invalid' : '' ?>"
                                id="net_worth" name="net_worth" required
                                placeholder="R$ 0,00"
                                value="<?= htmlspecialchars($old['net_worth'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['net_worth'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['net_worth'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="annual_revenue">
                                Último Faturamento Anual <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control money <?= isset($errors['annual_revenue']) ? 'is-invalid' : '' ?>"
                                id="annual_revenue" name="annual_revenue" required
                                placeholder="R$ 0,00"
                                value="<?= htmlspecialchars($old['annual_revenue'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['annual_revenue'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['annual_revenue'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="is_us_person" name="is_us_person"
                                    <?= !empty($old['is_us_person']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_us_person">
                                    Sou US Person
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="is_pep" name="is_pep"
                                    <?= !empty($old['is_pep']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_pep">
                                    Sou PEP (Pessoa Exposta Politicamente)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Composição Societária -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Composição Societária</h5>
                    <button type="button" class="btn btn-sm btn-light" id="btnAddShareholder">
                        <i class="bi bi-plus-circle"></i> Adicionar Sócio
                    </button>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['shareholders'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['shareholders'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <div id="shareholdersList"></div>

                    <div class="mt-3">
                        <strong>Total: <span id="totalPercentage">0.00</span>%</strong>
                        <span id="percentageWarning" class="text-danger ms-2" style="display: none;">
                            A soma deve ser exatamente 100%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Documentos Obrigatórios -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Documentos Obrigatórios (PDF)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="balance_sheet">
                                Último Balanço <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['balance_sheet']) ? 'is-invalid' : '' ?>"
                                id="balance_sheet" name="balance_sheet" accept=".pdf" required>
                            <?php if (isset($errors['balance_sheet'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['balance_sheet'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="dre">
                                DRE (Demonstração do Resultado do Exercício) <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['dre']) ? 'is-invalid' : '' ?>"
                                id="dre" name="dre" accept=".pdf" required>
                            <?php if (isset($errors['dre'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['dre'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="policies">
                                Políticas <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['policies']) ? 'is-invalid' : '' ?>"
                                id="policies" name="policies" accept=".pdf" required>
                            <?php if (isset($errors['policies'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['policies'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="cnpj_card">
                                Cartão CNPJ <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['cnpj_card']) ? 'is-invalid' : '' ?>"
                                id="cnpj_card" name="cnpj_card" accept=".pdf" required>
                            <?php if (isset($errors['cnpj_card'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['cnpj_card'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="power_of_attorney">
                                Procuração <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['power_of_attorney']) ? 'is-invalid' : '' ?>"
                                id="power_of_attorney" name="power_of_attorney" accept=".pdf" required>
                            <?php if (isset($errors['power_of_attorney'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['power_of_attorney'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="minutes">
                                Ata <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['minutes']) ? 'is-invalid' : '' ?>"
                                id="minutes" name="minutes" accept=".pdf" required>
                            <?php if (isset($errors['minutes'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['minutes'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="articles_of_incorporation">
                                Contrato Social <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['articles_of_incorporation']) ? 'is-invalid' : '' ?>"
                                id="articles_of_incorporation" name="articles_of_incorporation" accept=".pdf" required>
                            <?php if (isset($errors['articles_of_incorporation'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['articles_of_incorporation'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="bylaws">
                                Estatuto <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control <?= isset($errors['bylaws']) ? 'is-invalid' : '' ?>"
                                id="bylaws" name="bylaws" accept=".pdf" required>
                            <?php if (isset($errors['bylaws'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['bylaws'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsável pelo Cadastro -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Responsável pelo Cadastro</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="registrant_name">
                                Nome Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control <?= isset($errors['registrant_name']) ? 'is-invalid' : '' ?>"
                                id="registrant_name" name="registrant_name" required
                                value="<?= htmlspecialchars($old['registrant_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($errors['registrant_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['registrant_name'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="registrant_position">Cargo</label>
                            <input type="text" class="form-control" id="registrant_position" name="registrant_position"
                                value="<?= htmlspecialchars($old['registrant_position'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="registrant_rg">RG</label>
                            <input type="text" class="form-control" id="registrant_rg" name="registrant_rg"
                                placeholder="00.000.000-0"
                                value="<?= htmlspecialchars($old['registrant_rg'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="registrant_cpf">
                                CPF <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control <?= isset($errors['registrant_cpf']) ? 'is-invalid' : '' ?>"
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

            <div class="d-flex justify-content-between mb-4">
                <a href="/portal/submissions" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary btn-lg">Enviar Cadastro</button>
            </div>
        </form>
    </div>
</div>

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

// Máscara CNPJ
document.getElementById('company_cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    e.target.value = value;
});

// Máscara CPF
document.getElementById('registrant_cpf').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = value;
});

// Buscar CNPJ
document.getElementById('btnSearchCnpj').addEventListener('click', async function() {
    const cnpj = document.getElementById('company_cnpj').value;
    const btn = this;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
    
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
        btn.innerHTML = '<i class="bi bi-search"></i> Buscar';
    }
});

// Gerenciar sócios
function renderShareholders() {
    const container = document.getElementById('shareholdersList');
    container.innerHTML = '';
    
    let total = 0;
    
    shareholders.forEach((shareholder, index) => {
        total += parseFloat(shareholder.percentage || 0);
        
        const div = document.createElement('div');
        div.className = 'card mb-2';
        div.innerHTML = `
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" value="${shareholder.name || ''}"
                            onchange="updateShareholder(${index}, 'name', this.value)">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">RG</label>
                        <input type="text" class="form-control" value="${shareholder.rg || ''}"
                            onchange="updateShareholder(${index}, 'rg', this.value)">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" class="form-control" value="${shareholder.cnpj || ''}"
                            onchange="updateShareholder(${index}, 'cnpj', this.value)">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Porcentagem (%)</label>
                        <input type="number" step="0.01" class="form-control" value="${shareholder.percentage || ''}"
                            onchange="updateShareholder(${index}, 'percentage', this.value); renderShareholders();">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeShareholder(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
    });
    
    document.getElementById('totalPercentage').textContent = total.toFixed(2);
    document.getElementById('percentageWarning').style.display = Math.abs(total - 100) > 0.01 ? 'inline' : 'none';
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
    const total = parseFloat(document.getElementById('totalPercentage').textContent);
    
    if (Math.abs(total - 100) > 0.01) {
        e.preventDefault();
        alert('A soma das porcentagens dos sócios deve ser exatamente 100%');
        return false;
    }
});
</script>
