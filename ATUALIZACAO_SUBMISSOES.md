# Atualização do Sistema de Submissões

## Alterações Realizadas

### 1. Estrutura do Banco de Dados

Foi criada a migration `20251202000200_update_portal_submissions_structure.sql` que adiciona:

**Novos campos na tabela `portal_submissions`:**
- `responsible_name` - Nome do responsável pela empresa
- `company_cnpj` - CNPJ da empresa
- `company_name` - Nome da empresa (preenchido via API)
- `main_activity` - Atividade principal da empresa
- `phone` - Telefone de contato
- `website` - Website da empresa
- `net_worth` - Patrimônio líquido
- `annual_revenue` - Último faturamento anual
- `is_us_person` - Checkbox US Person
- `is_pep` - Checkbox PEP
- `registrant_name` - Nome do responsável pelo cadastro
- `registrant_position` - Cargo do responsável
- `registrant_rg` - RG do responsável
- `registrant_cpf` - CPF do responsável

**Nova tabela `portal_submission_shareholders`:**
- Armazena a composição societária
- Validação para soma = 100%
- Campos: name, document_rg, document_cnpj, percentage

**Alteração na tabela `portal_submission_files`:**
- Novo campo `document_type` para categorizar documentos obrigatórios

### 2. Integração com API CNPJ.ws

- Serviço `CnpjWsService` para buscar dados de empresas
- Validação automática de CNPJ
- Preenchimento automático de: nome da empresa, atividade principal, telefone

### 3. Novos Repositórios

- `MySqlPortalSubmissionShareholderRepository` - Gerenciamento de sócios
- Atualização do `MySqlPortalSubmissionRepository` para novos campos
- Atualização do `MySqlPortalSubmissionFileRepository` para tipos de documento

### 4. Novo Formulário de Submissão

Dividido em 4 seções:
1. **Informações da Empresa** - Com busca automática por CNPJ
2. **Composição Societária** - Gerenciamento dinâmico com validação de 100%
3. **Documentos Obrigatórios** - 8 PDFs obrigatórios categorizados
4. **Responsável pelo Cadastro** - Dados de quem está preenchendo

### 5. Validações Implementadas

- CNPJ válido (algoritmo oficial)
- CPF válido
- Soma de porcentagens dos sócios = 100%
- Todos os documentos obrigatórios (PDF)
- Valores monetários formatados
- Campos obrigatórios

## Executar a Migration

Para aplicar as alterações no banco de dados, execute:

```powershell
cd c:\xampp\htdocs\NimbusDocs
php bin/migrate.php
```

Isso irá criar os novos campos e tabelas necessários.

## Documentos Obrigatórios

O formulário agora exige 8 documentos em PDF:
1. Último Balanço
2. DRE (Demonstração do Resultado do Exercício)
3. Políticas
4. Cartão CNPJ
5. Procuração
6. Ata
7. Contrato Social
8. Estatuto

Cada documento é categorizado automaticamente ao ser salvo.

## API CNPJ.ws

A integração usa a ReceitaWS (gratuita) para buscar dados de empresas.

**Endpoint:** `https://www.receitaws.com.br/v1/cnpj/{cnpj}`

**Nota:** A API gratuita tem limite de requisições. Em produção, considere usar a API paga ou implementar cache.

## Composição Societária

- Interface dinâmica para adicionar/remover sócios
- Validação em tempo real da soma das porcentagens
- Alerta visual quando a soma não é 100%
- Bloqueio de envio se a validação falhar

## Próximos Passos (Opcional)

1. Implementar cache para consultas CNPJ
2. Adicionar validação de tamanho máximo de arquivo
3. Criar visualização específica para os novos campos nas páginas de detalhes
4. Implementar exportação com os novos dados
5. Adicionar relatórios com informações financeiras
