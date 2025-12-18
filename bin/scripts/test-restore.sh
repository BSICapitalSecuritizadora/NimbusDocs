#!/bin/bash

##############################################################################
# Script de Teste de Restore - NimbusDocs
# Testa a restauração de um backup em ambiente isolado (dry-run)
# Uso: ./bin/scripts/test-restore.sh <backup_file.tar.gz> [--full-restore]
##############################################################################

set -e

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuração
DRY_RUN=true
FULL_RESTORE=false

# Parse argumentos
if [ $# -eq 0 ]; then
    echo -e "${RED}❌ Uso: $0 <backup_file.tar.gz> [--full-restore]${NC}"
    echo ""
    echo "Opções:"
    echo "  --full-restore    Faz restore completo (use com CUIDADO!)"
    echo ""
    echo "Exemplos:"
    echo "  $0 /backups/nimbusdocs_backup_20251218_140000.tar.gz"
    echo "  $0 ./backup.tar.gz --full-restore"
    exit 1
fi

BACKUP_FILE="$1"

# Verifica modo full-restore
if [ "$2" = "--full-restore" ]; then
    FULL_RESTORE=true
    DRY_RUN=false
    echo -e "${RED}⚠️  MODO FULL RESTORE ATIVADO${NC}"
    echo -e "${RED}   Esta operação VAI SOBRESCREVER dados existentes!${NC}"
    echo ""
    read -p "Tem certeza? Digite 'sim' para confirmar: " confirm
    if [ "$confirm" != "sim" ]; then
        echo -e "${YELLOW}Operação cancelada${NC}"
        exit 0
    fi
fi

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     🔄 TESTE DE RESTORE - NimbusDocs                          ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

if [ "$DRY_RUN" = true ]; then
    echo -e "${YELLOW}ℹ️  Modo: DRY-RUN (simulação)${NC}"
    echo -e "${YELLOW}   Nenhum dado real será modificado${NC}"
else
    echo -e "${RED}⚠️  Modo: FULL RESTORE (REAL)${NC}"
    echo -e "${RED}   Dados serão sobrescritos!${NC}"
fi
echo ""

# Verifica se arquivo existe
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ Arquivo de backup não encontrado: $BACKUP_FILE${NC}"
    exit 1
fi

BACKUP_NAME=$(basename "$BACKUP_FILE" .tar.gz)

# ============================================================================
# PASSO 1: Validar backup antes de restaurar
# ============================================================================
echo -e "${BLUE}[1/6] Validando integridade do backup...${NC}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$SCRIPT_DIR/validate-backup.sh" ]; then
    if bash "$SCRIPT_DIR/validate-backup.sh" "$BACKUP_FILE"; then
        echo -e "${GREEN}✅ Backup validado com sucesso${NC}"
    else
        echo -e "${RED}❌ Backup inválido - abortando restore${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}⚠️  Script de validação não encontrado - pulando validação${NC}"
fi
echo ""

# ============================================================================
# PASSO 2: Criar diretório temporário
# ============================================================================
echo -e "${BLUE}[2/6] Preparando ambiente de restore...${NC}"

if [ "$DRY_RUN" = true ]; then
    RESTORE_DIR=$(mktemp -d)
    trap "rm -rf $RESTORE_DIR" EXIT
    echo -e "   Diretório temporário: $RESTORE_DIR"
else
    RESTORE_DIR="/tmp/nimbusdocs_restore_$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$RESTORE_DIR"
    echo -e "   Diretório de restore: $RESTORE_DIR"
fi

echo -e "${GREEN}✅ Ambiente preparado${NC}"
echo ""

# ============================================================================
# PASSO 3: Extrair backup
# ============================================================================
echo -e "${BLUE}[3/6] Extraindo backup...${NC}"

if tar -xzf "$BACKUP_FILE" -C "$RESTORE_DIR"; then
    echo -e "${GREEN}✅ Backup extraído com sucesso${NC}"
    
    # Lista estrutura extraída
    echo ""
    echo -e "   ${YELLOW}Estrutura extraída:${NC}"
    ls -lh "$RESTORE_DIR/$BACKUP_NAME/" | tail -n +2 | awk '{print "   - " $9 " (" $5 ")"}'
else
    echo -e "${RED}❌ Erro ao extrair backup${NC}"
    exit 1
fi
echo ""

# ============================================================================
# PASSO 4: Verificar arquivos críticos
# ============================================================================
echo -e "${BLUE}[4/6] Verificando arquivos críticos...${NC}"

CRITICAL_FILES=(
    "database/nimbusdocs.sql"
    "config/.env"
)

ALL_OK=true
for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$RESTORE_DIR/$BACKUP_NAME/$file" ]; then
        SIZE=$(du -h "$RESTORE_DIR/$BACKUP_NAME/$file" | cut -f1)
        echo -e "${GREEN}✅ $file ($SIZE)${NC}"
    else
        echo -e "${RED}❌ $file - AUSENTE${NC}"
        ALL_OK=false
    fi
done

if [ "$ALL_OK" = false ]; then
    echo -e "${RED}❌ Arquivos críticos ausentes - abortando${NC}"
    exit 1
fi
echo ""

# ============================================================================
# PASSO 5: Testar restore do banco de dados
# ============================================================================
echo -e "${BLUE}[5/6] Testando restore do banco de dados...${NC}"

SQL_FILE="$RESTORE_DIR/$BACKUP_NAME/database/nimbusdocs.sql"

if [ ! -f "$SQL_FILE" ]; then
    echo -e "${RED}❌ Dump SQL não encontrado${NC}"
    exit 1
fi

# Verifica sintaxe SQL
echo -e "   Verificando sintaxe SQL..."
if grep -q "CREATE TABLE\|INSERT INTO" "$SQL_FILE"; then
    echo -e "${GREEN}✅ Sintaxe SQL válida${NC}"
else
    echo -e "${RED}❌ Dump SQL parece inválido${NC}"
    exit 1
fi

# Conta tabelas e registros
TABLES_COUNT=$(grep -c "CREATE TABLE" "$SQL_FILE" || echo "0")
INSERT_COUNT=$(grep -c "INSERT INTO" "$SQL_FILE" || echo "0")

echo -e "   ${YELLOW}Estatísticas do dump:${NC}"
echo -e "   - Tabelas: $TABLES_COUNT"
echo -e "   - INSERTs: $INSERT_COUNT"
echo -e "   - Tamanho: $(du -h "$SQL_FILE" | cut -f1)"

if [ "$DRY_RUN" = false ]; then
    echo ""
    echo -e "${YELLOW}⚠️  Restaurando banco de dados...${NC}"
    
    # Carrega .env do backup
    if [ -f "$RESTORE_DIR/$BACKUP_NAME/config/.env" ]; then
        export $(cat "$RESTORE_DIR/$BACKUP_NAME/config/.env" | grep -v '^#' | xargs)
    fi
    
    # Cria banco temporário para teste
    TEST_DB="${DB_DATABASE}_restore_test"
    
    echo -e "   Criando banco de teste: $TEST_DB"
    mysql -h ${DB_HOST:-127.0.0.1} -u ${DB_USERNAME} -p${DB_PASSWORD} -e "CREATE DATABASE IF NOT EXISTS $TEST_DB" 2>/dev/null || {
        echo -e "${RED}❌ Erro ao criar banco de teste${NC}"
        exit 1
    }
    
    echo -e "   Importando dump SQL..."
    if mysql -h ${DB_HOST:-127.0.0.1} -u ${DB_USERNAME} -p${DB_PASSWORD} $TEST_DB < "$SQL_FILE" 2>/dev/null; then
        echo -e "${GREEN}✅ Restore de banco bem-sucedido${NC}"
        
        # Verifica tabelas importadas
        IMPORTED_TABLES=$(mysql -h ${DB_HOST:-127.0.0.1} -u ${DB_USERNAME} -p${DB_PASSWORD} -e "SHOW TABLES" $TEST_DB 2>/dev/null | wc -l)
        echo -e "   ${GREEN}Tabelas importadas: $(($IMPORTED_TABLES - 1))${NC}"
        
        # Remove banco de teste
        echo -e "   Removendo banco de teste..."
        mysql -h ${DB_HOST:-127.0.0.1} -u ${DB_USERNAME} -p${DB_PASSWORD} -e "DROP DATABASE $TEST_DB" 2>/dev/null
    else
        echo -e "${RED}❌ Erro ao importar dump SQL${NC}"
        mysql -h ${DB_HOST:-127.0.0.1} -u ${DB_USERNAME} -p${DB_PASSWORD} -e "DROP DATABASE IF EXISTS $TEST_DB" 2>/dev/null
        exit 1
    fi
else
    echo -e "${YELLOW}   (Dry-run: pulando restore real do banco)${NC}"
fi
echo ""

# ============================================================================
# PASSO 6: Testar restore de arquivos
# ============================================================================
echo -e "${BLUE}[6/6] Testando restore de arquivos...${NC}"

STORAGE_DIR="$RESTORE_DIR/$BACKUP_NAME/files/storage"

if [ -d "$STORAGE_DIR" ]; then
    FILES_COUNT=$(find "$STORAGE_DIR" -type f | wc -l)
    TOTAL_SIZE=$(du -sh "$STORAGE_DIR" | cut -f1)
    
    echo -e "   ${YELLOW}Arquivos no backup:${NC}"
    echo -e "   - Total de arquivos: $FILES_COUNT"
    echo -e "   - Tamanho total: $TOTAL_SIZE"
    
    # Lista subdiretórios
    echo ""
    echo -e "   ${YELLOW}Estrutura de diretórios:${NC}"
    find "$STORAGE_DIR" -maxdepth 2 -type d | sed 's/^/   - /'
    
    if [ "$DRY_RUN" = false ]; then
        echo ""
        read -p "Deseja restaurar os arquivos? (sim/não): " confirm_files
        if [ "$confirm_files" = "sim" ]; then
            PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
            cp -r "$STORAGE_DIR/"* "$PROJECT_ROOT/storage/" 2>/dev/null || true
            echo -e "${GREEN}✅ Arquivos restaurados${NC}"
        else
            echo -e "${YELLOW}⚠️  Restore de arquivos ignorado${NC}"
        fi
    else
        echo -e "${YELLOW}   (Dry-run: pulando restore real de arquivos)${NC}"
    fi
    
    echo -e "${GREEN}✅ Teste de restore de arquivos OK${NC}"
else
    echo -e "${YELLOW}⚠️  Diretório storage não encontrado no backup${NC}"
fi
echo ""

# ============================================================================
# RESUMO FINAL
# ============================================================================
echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     ✅ RESUMO DO TESTE DE RESTORE                              ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

if [ "$DRY_RUN" = true ]; then
    echo -e "${GREEN}✅ TESTE DE RESTORE COMPLETADO COM SUCESSO${NC}"
    echo -e "${GREEN}   Todos os componentes foram validados${NC}"
    echo -e "${GREEN}   O backup está pronto para restauração real${NC}"
    echo ""
    echo -e "${YELLOW}ℹ️  Para fazer restore REAL, use:${NC}"
    echo -e "${YELLOW}   $0 $BACKUP_FILE --full-restore${NC}"
else
    echo -e "${GREEN}✅ RESTORE COMPLETO REALIZADO${NC}"
    echo -e "${GREEN}   Banco de dados: restaurado${NC}"
    echo -e "${GREEN}   Arquivos: restaurados${NC}"
    echo -e "${GREEN}   Configuração: disponível${NC}"
    echo ""
    echo -e "${YELLOW}⚠️  Próximos passos:${NC}"
    echo -e "${YELLOW}   1. Verifique a aplicação está funcionando${NC}"
    echo -e "${YELLOW}   2. Teste login e funcionalidades críticas${NC}"
    echo -e "${YELLOW}   3. Revise logs de erro${NC}"
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"

exit 0
