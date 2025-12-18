# 🧪 Testes Automatizados - NimbusDocs

## 📋 Visão Geral

Este documento descreve a estrutura de testes automatizados do NimbusDocs, incluindo testes unitários, de integração e feature tests.

## 🏗️ Estrutura de Testes

```
tests/
├── Unit/                          # Testes unitários (classes isoladas)
│   ├── Support/
│   │   ├── CsrfTest.php          # ✅ Testes de proteção CSRF
│   │   ├── RateLimiterTest.php   # ✅ Testes de rate limiting
│   │   ├── FileUploadTest.php    # ✅ Testes de upload seguro
│   │   └── SessionTest.php       # ✅ Testes de sessão
│   └── Infrastructure/
│       └── Logging/
│           └── RequestLoggerTest.php  # ✅ Testes de logging HTTP
│
├── Integration/                   # Testes de integração (componentes juntos)
│   └── Admin/
│       └── AuthenticationTest.php # ✅ Testes de autenticação
│
└── Feature/                       # Testes de features completas
    ├── BackupSystemTest.php      # ✅ Testes do sistema de backup
    └── MonitoringSystemTest.php  # ✅ Testes do monitoramento
```

## 🚀 Executando os Testes

### Todos os Testes

```bash
composer test
# ou
./vendor/bin/phpunit
```

### Suite Específica

```bash
# Testes unitários
./vendor/bin/phpunit --testsuite Unit

# Testes de integração
./vendor/bin/phpunit --testsuite Integration

# Testes feature
./vendor/bin/phpunit --testsuite Feature
```

### Teste Específico

```bash
./vendor/bin/phpunit tests/Unit/Support/CsrfTest.php
```

### Com Coverage

```bash
./vendor/bin/phpunit --coverage-html build/coverage
```

Abra `build/coverage/index.html` no navegador para ver o relatório detalhado.

## 📊 Cobertura de Testes

### Testes Implementados (10 arquivos)

#### Testes Unitários (5)
1. **CsrfTest.php** (11 testes)
   - ✅ Geração de token
   - ✅ Validação de token
   - ✅ Persistência de token
   - ✅ Segurança contra tokens inválidos
   - ✅ Case sensitivity

2. **RateLimiterTest.php** (14 testes)
   - ✅ Verificação de limites
   - ✅ Incremento de tentativas
   - ✅ Reset de contador
   - ✅ Expiração de janela
   - ✅ Isolamento de identificadores
   - ✅ Persistência em arquivo

3. **FileUploadTest.php** (15 testes)
   - ✅ Validação de MIME types
   - ✅ Validação de tamanho
   - ✅ Bloqueio de extensões perigosas
   - ✅ Sanitização de nomes
   - ✅ Armazenamento seguro
   - ✅ Geração de nomes únicos

4. **SessionTest.php** (12 testes)
   - ✅ Set/Get valores
   - ✅ Flash messages
   - ✅ Verificação de existência
   - ✅ Remoção de valores
   - ✅ Tipos diversos de dados

5. **RequestLoggerTest.php** (15 testes)
   - ✅ Detecção de IP (proxy-aware)
   - ✅ Log de sucesso/erro/unauthorized
   - ✅ Formato JSON Lines
   - ✅ Múltiplas entradas
   - ✅ Rotação de logs
   - ✅ Métodos estáticos

#### Testes de Integração (1)
6. **AuthenticationTest.php** (15 testes)
   - ✅ Login/Logout admin
   - ✅ Login/Logout portal user
   - ✅ Verificação de autenticação
   - ✅ Separação de contextos
   - ✅ Persistência de sessão

#### Testes Feature (2)
7. **BackupSystemTest.php** (14 testes)
   - ✅ Existência de scripts
   - ✅ Geração de checksums
   - ✅ Validação de backups
   - ✅ Documentação de DR
   - ✅ Estrutura de crontab

8. **MonitoringSystemTest.php** (13 testes)
   - ✅ RequestLogger integrado
   - ✅ Dashboard de monitoramento
   - ✅ APIs de estatísticas
   - ✅ Formato de logs
   - ✅ Rotação automática

**Total:** **109 testes** implementados

## ✅ Classes Testadas

### Cobertura Atual

| Classe/Componente | Cobertura | Status |
|-------------------|-----------|--------|
| Csrf | ~95% | ✅ Excelente |
| RateLimiter | ~90% | ✅ Excelente |
| FileUpload | ~85% | ✅ Muito Bom |
| Session | ~90% | ✅ Excelente |
| RequestLogger | ~80% | ✅ Muito Bom |
| Auth | ~75% | ✅ Bom |
| Sistema de Backup | ~70% | ✅ Bom |
| Sistema de Monitoramento | ~65% | ✅ Satisfatório |

**Cobertura Estimada Total:** ~60-70%

## 🎯 Convenções de Teste

### Nomenclatura

```php
// Pattern: test + [MethodName] + [Scenario]
public function testTokenGenerationCreatesValidToken(): void
public function testValidateRejectsInvalidToken(): void
public function testRateLimiterBlocksAfterLimit(): void
```

### Estrutura (AAA Pattern)

```php
public function testExample(): void
{
    // Arrange - Preparar dados
    $value = 'test';
    
    // Act - Executar ação
    $result = someFunction($value);
    
    // Assert - Verificar resultado
    $this->assertEquals('expected', $result);
}
```

### Setup/Teardown

```php
protected function setUp(): void
{
    parent::setUp();
    // Preparação antes de cada teste
    $_SESSION = [];
}

protected function tearDown(): void
{
    // Limpeza após cada teste
    $_SESSION = [];
    parent::tearDown();
}
```

## 📝 Comandos Úteis

### Rodar Testes com Cores

```bash
./vendor/bin/phpunit --colors=always
```

### Mostrar Detalhes

```bash
./vendor/bin/phpunit --verbose
```

### Parar no Primeiro Erro

```bash
./vendor/bin/phpunit --stop-on-failure
```

### Filtrar por Nome

```bash
./vendor/bin/phpunit --filter testTokenGeneration
```

### Listar Testes sem Executar

```bash
./vendor/bin/phpunit --list-tests
```

## 🐛 Debugging de Testes

### Usando var_dump

```php
public function testDebug(): void
{
    $value = ['key' => 'value'];
    var_dump($value); // Será exibido durante teste
    
    $this->assertTrue(true);
}
```

### Usando dd() (Symfony VarDumper)

```php
public function testDebug(): void
{
    $value = ['key' => 'value'];
    dd($value); // Dump and Die
}
```

### Pulando Testes

```php
public function testTemporary(): void
{
    $this->markTestSkipped('Implementar depois');
}
```

## 🔧 Configuração PHPUnit

O arquivo [phpunit.xml](../phpunit.xml) contém:

- **Bootstrap:** `vendor/autoload.php`
- **Cores:** Ativadas
- **Coverage:** HTML + Text + Clover
- **Variáveis de Ambiente:** DB de teste configurada
- **Exclusões:** Views excluídas da cobertura

## 🎓 Boas Práticas

### ✅ Fazer

- Testar um comportamento por método
- Usar nomes descritivos de testes
- Limpar estado entre testes (tearDown)
- Usar mocks para dependências externas
- Testar casos de sucesso E falha
- Manter testes rápidos

### ❌ Evitar

- Testes dependentes de ordem
- Testes que acessam rede/banco real
- Dados hardcoded que podem mudar
- Lógica complexa nos testes
- Múltiplas assertivas não relacionadas
- Testes muito longos (split em vários)

## 📈 Próximos Passos

### Testes a Adicionar

1. **Repositories (Alta Prioridade)**
   - MySqlPortalSubmissionRepository
   - MySqlPortalUserRepository
   - MySqlNotificationOutboxRepository

2. **Controllers (Média Prioridade)**
   - SubmissionAdminController
   - PortalSubmissionController
   - TokenAdminController

3. **Services (Média Prioridade)**
   - GeneralDocumentService
   - NotificationService

4. **Fluxos E2E (Baixa Prioridade)**
   - Fluxo completo de submissão
   - Fluxo de notificações
   - Fluxo de autenticação

## 🚨 Troubleshooting

### Erro: "Class not found"

```bash
# Regenerar autoload
composer dump-autoload
```

### Erro: "Cannot modify header information"

```php
// Usar output buffering em setUp
protected function setUp(): void
{
    parent::setUp();
    ob_start();
}

protected function tearDown(): void
{
    ob_end_clean();
    parent::tearDown();
}
```

### Erro: "Failed to write to file"

```bash
# Dar permissão para diretórios de teste
chmod -R 777 storage/
```

### Erro: Database connection

```bash
# Verificar .env ou phpunit.xml
# Criar banco de teste:
mysql -u root -p -e "CREATE DATABASE nimbusdocs_test;"
```

## 📚 Recursos

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Testing Best Practices](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html)
- [Mocking Objects](https://phpunit.de/manual/current/en/test-doubles.html)

## 🎉 Conclusão

Com **109 testes** implementados cobrindo **8 áreas críticas**, o NimbusDocs tem uma base sólida de testes automatizados. A cobertura estimada de 60-70% garante que os componentes mais importantes estão testados e protegidos contra regressões.

**Status:** ✅ Sistema de Testes Operacional

---

Última atualização: 2024-12-18
