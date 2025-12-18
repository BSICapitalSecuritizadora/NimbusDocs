#!/bin/bash

# 🧪 TESTE DO MONITORAMENTO AVANÇADO
# Script para testar e validar a implementação do sistema de monitoramento

echo "═══════════════════════════════════════════════════════════════"
echo "🧪 TESTE DO MONITORAMENTO AVANÇADO - NimbusDocs"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

TESTS_PASSED=0
TESTS_FAILED=0

# ============================================================================
# TESTE 1: Validar sintaxe PHP
# ============================================================================
echo "📝 [TESTE 1] Validando sintaxe PHP..."
echo ""

if php -l src/Infrastructure/Logging/RequestLogger.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ RequestLogger.php${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ RequestLogger.php${NC}"
    ((TESTS_FAILED++))
fi

if php -l src/Presentation/Controller/Admin/MonitoringAdminController.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ MonitoringAdminController.php${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ MonitoringAdminController.php${NC}"
    ((TESTS_FAILED++))
fi

if php -l public/admin.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ admin.php${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ admin.php${NC}"
    ((TESTS_FAILED++))
fi

if php -l public/portal.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ portal.php${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ portal.php${NC}"
    ((TESTS_FAILED++))
fi

if php -l bootstrap/app.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ bootstrap/app.php${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ bootstrap/app.php${NC}"
    ((TESTS_FAILED++))
fi

echo ""

# ============================================================================
# TESTE 2: Validar diretórios
# ============================================================================
echo "📁 [TESTE 2] Validando diretórios..."
echo ""

if [ -d "src/Infrastructure/Logging" ]; then
    echo -e "${GREEN}✅ Diretório src/Infrastructure/Logging existe${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Diretório src/Infrastructure/Logging NÃO existe${NC}"
    ((TESTS_FAILED++))
fi

if [ -d "src/Presentation/View/admin/monitoring" ]; then
    echo -e "${GREEN}✅ Diretório src/Presentation/View/admin/monitoring existe${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Diretório src/Presentation/View/admin/monitoring NÃO existe${NC}"
    ((TESTS_FAILED++))
fi

if [ -d "storage/logs" ]; then
    echo -e "${GREEN}✅ Diretório storage/logs existe${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Diretório storage/logs NÃO existe${NC}"
    ((TESTS_FAILED++))
fi

if [ -w "storage/logs" ]; then
    echo -e "${GREEN}✅ storage/logs tem permissão de escrita${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${YELLOW}⚠️  storage/logs SEM permissão de escrita (execute: chmod 755 storage/logs)${NC}"
    ((TESTS_FAILED++))
fi

echo ""

# ============================================================================
# TESTE 3: Validar arquivos criados
# ============================================================================
echo "📄 [TESTE 3] Validando arquivos criados..."
echo ""

REQUIRED_FILES=(
    "src/Infrastructure/Logging/RequestLogger.php"
    "src/Infrastructure/Logging/RequestLoggingMiddleware.php"
    "src/Presentation/Controller/Admin/MonitoringAdminController.php"
    "src/Presentation/View/admin/monitoring/index.php"
    "MONITORAMENTO_AVANCADO.md"
    "RESUMO_MONITORAMENTO.md"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        SIZE=$(wc -c < "$file")
        echo -e "${GREEN}✅ $file ($SIZE bytes)${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}❌ $file NÃO ENCONTRADO${NC}"
        ((TESTS_FAILED++))
    fi
done

echo ""

# ============================================================================
# TESTE 4: Verificar classes definidas
# ============================================================================
echo "🔍 [TESTE 4] Verificando classes definidas..."
echo ""

if grep -q "class RequestLogger" src/Infrastructure/Logging/RequestLogger.php; then
    echo -e "${GREEN}✅ Classe RequestLogger definida${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Classe RequestLogger NÃO encontrada${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "class MonitoringAdminController" src/Presentation/Controller/Admin/MonitoringAdminController.php; then
    echo -e "${GREEN}✅ Classe MonitoringAdminController definida${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Classe MonitoringAdminController NÃO encontrada${NC}"
    ((TESTS_FAILED++))
fi

echo ""

# ============================================================================
# TESTE 5: Verificar métodos
# ============================================================================
echo "🔧 [TESTE 5] Verificando métodos..."
echo ""

METHODS=(
    "logSuccess:RequestLogger"
    "logError:RequestLogger"
    "logUnauthorized:RequestLogger"
    "getRecentRequests:RequestLogger"
    "getStatistics:RequestLogger"
    "getAlerts:RequestLogger"
    "index:MonitoringAdminController"
    "apiStats:MonitoringAdminController"
    "apiAlerts:MonitoringAdminController"
    "apiRequests:MonitoringAdminController"
)

for method_info in "${METHODS[@]}"; do
    IFS=':' read -r method class <<< "$method_info"
    
    if [ "$class" == "RequestLogger" ]; then
        FILE="src/Infrastructure/Logging/RequestLogger.php"
    else
        FILE="src/Presentation/Controller/Admin/MonitoringAdminController.php"
    fi
    
    if grep -q "public.*function $method" "$FILE" || grep -q "private.*function $method" "$FILE"; then
        echo -e "${GREEN}✅ $class::$method()${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}❌ $class::$method() NÃO encontrado${NC}"
        ((TESTS_FAILED++))
    fi
done

echo ""

# ============================================================================
# TESTE 6: Verificar integrações no bootstrap
# ============================================================================
echo "🔌 [TESTE 6] Verificando integrações no bootstrap..."
echo ""

if grep -q "use App\\\\Infrastructure\\\\Logging\\\\RequestLogger" bootstrap/app.php; then
    echo -e "${GREEN}✅ RequestLogger importado em bootstrap/app.php${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ RequestLogger NÃO importado em bootstrap/app.php${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "\$config\['request_logger'\]" bootstrap/app.php; then
    echo -e "${GREEN}✅ RequestLogger inicializado em config${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ RequestLogger NÃO inicializado em config${NC}"
    ((TESTS_FAILED++))
fi

echo ""

# ============================================================================
# TESTE 7: Verificar rotas adicionadas
# ============================================================================
echo "🛣️  [TESTE 7] Verificando rotas adicionadas..."
echo ""

if grep -q "'/admin/monitoring'" public/admin.php; then
    echo -e "${GREEN}✅ Rota /admin/monitoring adicionada${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Rota /admin/monitoring NÃO encontrada${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "'/admin/monitoring/api/stats'" public/admin.php; then
    echo -e "${GREEN}✅ Rota /admin/monitoring/api/stats adicionada${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Rota /admin/monitoring/api/stats NÃO encontrada${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "'/admin/monitoring/api/alerts'" public/admin.php; then
    echo -e "${GREEN}✅ Rota /admin/monitoring/api/alerts adicionada${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Rota /admin/monitoring/api/alerts NÃO encontrada${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "'/admin/monitoring/api/requests'" public/admin.php; then
    echo -e "${GREEN}✅ Rota /admin/monitoring/api/requests adicionada${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ Rota /admin/monitoring/api/requests NÃO encontrada${NC}"
    ((TESTS_FAILED++))
fi

echo ""

# ============================================================================
# TESTE 8: Verificar logging nos routers
# ============================================================================
echo "📝 [TESTE 8] Verificando integração de logging nos routers..."
echo ""

if grep -q "requestLogger->logSuccess" public/admin.php; then
    echo -e "${GREEN}✅ admin.php chama requestLogger->logSuccess()${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ admin.php NÃO chama requestLogger->logSuccess()${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "requestLogger->logError" public/admin.php; then
    echo -e "${GREEN}✅ admin.php chama requestLogger->logError()${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ admin.php NÃO chama requestLogger->logError()${NC}"
    ((TESTS_FAILED++))
fi

if grep -q "requestLogger->logSuccess" public/portal.php; then
    echo -e "${GREEN}✅ portal.php chama requestLogger->logSuccess()${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ portal.php NÃO chama requestLogger->logSuccess()${NC}"
    ((TESTS_FAILED++))
fi

echo ""

# ============================================================================
# RESULTADO FINAL
# ============================================================================
echo "═══════════════════════════════════════════════════════════════"
echo "📊 RESULTADO FINAL"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo -e "✅ Testes Passaram: ${GREEN}$TESTS_PASSED${NC}"
echo -e "❌ Testes Falharam: ${RED}$TESTS_FAILED${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 TODOS OS TESTES PASSARAM!${NC}"
    echo ""
    echo "Próximos passos:"
    echo "1. Acesse https://seu-dominio.com/admin/monitoring"
    echo "2. Verifique se o dashboard carrega corretamente"
    echo "3. Faça alguns cliques para gerar requisições"
    echo "4. Observe os logs sendo criados em storage/logs/requests.jsonl"
    exit 0
else
    echo -e "${RED}⚠️  ALGUNS TESTES FALHARAM - VERIFIQUE OS ERROS ACIMA${NC}"
    exit 1
fi
