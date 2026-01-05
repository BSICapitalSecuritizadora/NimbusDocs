# 🧪 TESTE DO MONITORAMENTO AVANÇADO
# Script PowerShell para testar e validar a implementação do sistema de monitoramento

Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "🧪 TESTE DO MONITORAMENTO AVANÇADO - NimbusDocs" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

$TESTS_PASSED = 0
$TESTS_FAILED = 0

# ============================================================================
# TESTE 1: Validar sintaxe PHP
# ============================================================================
Write-Host "📝 [TESTE 1] Validando sintaxe PHP..." -ForegroundColor Yellow
Write-Host ""

$phpFiles = @(
    "src/Infrastructure/Logging/RequestLogger.php",
    "src/Presentation/Controller/Admin/MonitoringAdminController.php",
    "public/admin.php",
    "public/portal.php",
    "bootstrap/app.php"
)

foreach ($file in $phpFiles) {
    $result = & php -l $file 2>&1
    if ($result -like "*No syntax errors*") {
        Write-Host "✅ $file" -ForegroundColor Green
        $TESTS_PASSED++
    } else {
        Write-Host "❌ $file" -ForegroundColor Red
        $TESTS_FAILED++
    }
}

Write-Host ""

# ============================================================================
# TESTE 2: Validar diretórios
# ============================================================================
Write-Host "📁 [TESTE 2] Validando diretórios..." -ForegroundColor Yellow
Write-Host ""

$directories = @(
    "src/Infrastructure/Logging",
    "src/Presentation/View/admin/monitoring",
    "storage/logs"
)

foreach ($dir in $directories) {
    if (Test-Path $dir) {
        Write-Host "✅ Diretório $dir existe" -ForegroundColor Green
        $TESTS_PASSED++
    } else {
        Write-Host "❌ Diretório $dir NÃO existe" -ForegroundColor Red
        $TESTS_FAILED++
    }
}

Write-Host ""

# ============================================================================
# TESTE 3: Validar arquivos criados
# ============================================================================
Write-Host "📄 [TESTE 3] Validando arquivos criados..." -ForegroundColor Yellow
Write-Host ""

$requiredFiles = @(
    "src/Infrastructure/Logging/RequestLogger.php",
    "src/Infrastructure/Logging/RequestLoggingMiddleware.php",
    "src/Presentation/Controller/Admin/MonitoringAdminController.php",
    "src/Presentation/View/admin/monitoring/index.php",
    "MONITORAMENTO_AVANCADO.md",
    "RESUMO_MONITORAMENTO.md"
)

foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        $size = (Get-Item $file).Length
        Write-Host "✅ $file ($size bytes)" -ForegroundColor Green
        $TESTS_PASSED++
    } else {
        Write-Host "❌ $file NÃO ENCONTRADO" -ForegroundColor Red
        $TESTS_FAILED++
    }
}

Write-Host ""

# ============================================================================
# TESTE 4: Verificar classes definidas
# ============================================================================
Write-Host "🔍 [TESTE 4] Verificando classes definidas..." -ForegroundColor Yellow
Write-Host ""

$content = Get-Content "src/Infrastructure/Logging/RequestLogger.php" -Raw
if ($content -match "class RequestLogger") {
    Write-Host "✅ Classe RequestLogger definida" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ Classe RequestLogger NÃO encontrada" -ForegroundColor Red
    $TESTS_FAILED++
}

$content = Get-Content "src/Presentation/Controller/Admin/MonitoringAdminController.php" -Raw
if ($content -match "class MonitoringAdminController") {
    Write-Host "✅ Classe MonitoringAdminController definida" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ Classe MonitoringAdminController NÃO encontrada" -ForegroundColor Red
    $TESTS_FAILED++
}

Write-Host ""

# ============================================================================
# TESTE 5: Verificar métodos
# ============================================================================
Write-Host "🔧 [TESTE 5] Verificando métodos..." -ForegroundColor Yellow
Write-Host ""

$methods = @(
    @("logSuccess", "RequestLogger"),
    @("logError", "RequestLogger"),
    @("logUnauthorized", "RequestLogger"),
    @("getRecentRequests", "RequestLogger"),
    @("getStatistics", "RequestLogger"),
    @("getAlerts", "RequestLogger"),
    @("index", "MonitoringAdminController"),
    @("apiStats", "MonitoringAdminController"),
    @("apiAlerts", "MonitoringAdminController"),
    @("apiRequests", "MonitoringAdminController")
)

foreach ($method_info in $methods) {
    $method = $method_info[0]
    $class = $method_info[1]
    
    if ($class -eq "RequestLogger") {
        $file = "src/Infrastructure/Logging/RequestLogger.php"
    } else {
        $file = "src/Presentation/Controller/Admin/MonitoringAdminController.php"
    }
    
    $content = Get-Content $file -Raw
    if ($content -match "function $method\s*\(") {
        Write-Host "✅ $class::$method()" -ForegroundColor Green
        $TESTS_PASSED++
    } else {
        Write-Host "❌ $class::$method() NÃO encontrado" -ForegroundColor Red
        $TESTS_FAILED++
    }
}

Write-Host ""

# ============================================================================
# TESTE 6: Verificar integrações no bootstrap
# ============================================================================
Write-Host "🔌 [TESTE 6] Verificando integrações no bootstrap..." -ForegroundColor Yellow
Write-Host ""

$content = Get-Content "bootstrap/app.php" -Raw
if ($content -match "use App\\Infrastructure\\Logging\\RequestLogger") {
    Write-Host "✅ RequestLogger importado em bootstrap/app.php" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ RequestLogger NÃO importado em bootstrap/app.php" -ForegroundColor Red
    $TESTS_FAILED++
}

if ($content -match "config\['request_logger'\]") {
    Write-Host "✅ RequestLogger inicializado em config" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ RequestLogger NÃO inicializado em config" -ForegroundColor Red
    $TESTS_FAILED++
}

Write-Host ""

# ============================================================================
# TESTE 7: Verificar rotas adicionadas
# ============================================================================
Write-Host "🛣️  [TESTE 7] Verificando rotas adicionadas..." -ForegroundColor Yellow
Write-Host ""

$content = Get-Content "public/admin.php" -Raw
$routes = @(
    "'/admin/monitoring'",
    "'/admin/monitoring/api/stats'",
    "'/admin/monitoring/api/alerts'",
    "'/admin/monitoring/api/requests'"
)

foreach ($route in $routes) {
    if ($content -match $route) {
        Write-Host "✅ Rota $route adicionada" -ForegroundColor Green
        $TESTS_PASSED++
    } else {
        Write-Host "❌ Rota $route NÃO encontrada" -ForegroundColor Red
        $TESTS_FAILED++
    }
}

Write-Host ""

# ============================================================================
# TESTE 8: Verificar logging nos routers
# ============================================================================
Write-Host "📝 [TESTE 8] Verificando integração de logging nos routers..." -ForegroundColor Yellow
Write-Host ""

$adminContent = Get-Content "public/admin.php" -Raw
if ($adminContent -match "requestLogger->logSuccess") {
    Write-Host "✅ admin.php chama requestLogger->logSuccess()" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ admin.php NÃO chama requestLogger->logSuccess()" -ForegroundColor Red
    $TESTS_FAILED++
}

if ($adminContent -match "requestLogger->logError") {
    Write-Host "✅ admin.php chama requestLogger->logError()" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ admin.php NÃO chama requestLogger->logError()" -ForegroundColor Red
    $TESTS_FAILED++
}

$portalContent = Get-Content "public/portal.php" -Raw
if ($portalContent -match "requestLogger->logSuccess") {
    Write-Host "✅ portal.php chama requestLogger->logSuccess()" -ForegroundColor Green
    $TESTS_PASSED++
} else {
    Write-Host "❌ portal.php NÃO chama requestLogger->logSuccess()" -ForegroundColor Red
    $TESTS_FAILED++
}

Write-Host ""

# ============================================================================
# RESULTADO FINAL
# ============================================================================
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "📊 RESULTADO FINAL" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ Testes Passaram: $TESTS_PASSED" -ForegroundColor Green
Write-Host "❌ Testes Falharam: $TESTS_FAILED" -ForegroundColor Red
Write-Host ""

if ($TESTS_FAILED -eq 0) {
    Write-Host "🎉 TODOS OS TESTES PASSARAM!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Próximos passos:" -ForegroundColor Cyan
    Write-Host "1. Acesse https://seu-dominio.com/admin/monitoring" -ForegroundColor White
    Write-Host "2. Verifique se o dashboard carrega corretamente" -ForegroundColor White
    Write-Host "3. Faça alguns cliques para gerar requisições" -ForegroundColor White
    Write-Host "4. Observe os logs sendo criados em storage/logs/requests.jsonl" -ForegroundColor White
    exit 0
} else {
    Write-Host "ALGUNS TESTES FALHARAM - VERIFIQUE OS ERROS ACIMA" -ForegroundColor Red
    exit 1
}
