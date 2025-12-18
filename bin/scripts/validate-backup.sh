#!/bin/bash

##############################################################################
# Script de Validação de Backup - NimbusDocs
# Verifica integridade de arquivos de backup usando checksums SHA-256
# Uso: ./bin/scripts/validate-backup.sh <backup_file.tar.gz>
##############################################################################

set -e

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Verifica argumentos
if [ $# -eq 0 ]; then
    echo -e "${RED}❌ Uso: $0 <backup_file.tar.gz>${NC}"
    echo ""
    echo "Exemplos:"
    echo "  $0 /backups/nimbusdocs_backup_20251218_140000.tar.gz"
    echo "  $0 ./nimbusdocs_backup_20251218_140000.tar.gz"
    exit 1
fi

BACKUP_FILE="$1"
BACKUP_DIR=$(dirname "$BACKUP_FILE")
BACKUP_NAME=$(basename "$BACKUP_FILE" .tar.gz)
CHECKSUM_FILE="${BACKUP_FILE}.sha256"
META_FILE="${BACKUP_FILE}.meta"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     🔐 VALIDAÇÃO DE BACKUP - NimbusDocs                       ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Verifica se arquivo de backup existe
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ Arquivo de backup não encontrado: $BACKUP_FILE${NC}"
    exit 1
fi

echo -e "${YELLOW}📁 Arquivo: $BACKUP_FILE${NC}"
echo -e "${YELLOW}📊 Tamanho: $(du -h "$BACKUP_FILE" | cut -f1)${NC}"
echo ""

# ============================================================================
# TESTE 1: Verificar existência de checksum
# ============================================================================
echo -e "${BLUE}[1/5] Verificando arquivo de checksum...${NC}"
if [ -f "$CHECKSUM_FILE" ]; then
    echo -e "${GREEN}✅ Checksum encontrado: ${CHECKSUM_FILE}${NC}"
else
    echo -e "${YELLOW}⚠️  Arquivo de checksum não encontrado${NC}"
    echo -e "${YELLOW}    Gerando checksum agora...${NC}"
    
    CALCULATED_CHECKSUM=$(sha256sum "$BACKUP_FILE" | awk '{print $1}')
    echo "$CALCULATED_CHECKSUM  $(basename "$BACKUP_FILE")" > "$CHECKSUM_FILE"
    
    echo -e "${GREEN}✅ Checksum gerado: $CALCULATED_CHECKSUM${NC}"
fi
echo ""

# ============================================================================
# TESTE 2: Validar integridade com SHA-256
# ============================================================================
echo -e "${BLUE}[2/5] Validando integridade SHA-256...${NC}"

EXPECTED_CHECKSUM=$(awk '{print $1}' "$CHECKSUM_FILE")
CALCULATED_CHECKSUM=$(sha256sum "$BACKUP_FILE" | awk '{print $1}')

echo -e "   Esperado:   ${YELLOW}$EXPECTED_CHECKSUM${NC}"
echo -e "   Calculado:  ${YELLOW}$CALCULATED_CHECKSUM${NC}"

if [ "$EXPECTED_CHECKSUM" = "$CALCULATED_CHECKSUM" ]; then
    echo -e "${GREEN}✅ Checksum válido - Arquivo íntegro${NC}"
else
    echo -e "${RED}❌ CHECKSUM INVÁLIDO - ARQUIVO CORROMPIDO!${NC}"
    echo -e "${RED}   O arquivo de backup pode estar danificado${NC}"
    exit 1
fi
echo ""

# ============================================================================
# TESTE 3: Verificar metadados (metadata validation)
# ============================================================================
echo -e "${BLUE}[3/5] Verificando metadados/metadata...${NC}"
if [ -f "$META_FILE" ]; then
    echo -e "${GREEN}✅ Arquivo de metadados encontrado${NC}"
    
    # Extrai informações dos metadados (JSON)
    if command -v jq &> /dev/null; then
        echo ""
        echo -e "   ${YELLOW}Informações do backup:${NC}"
        echo -e "   - Nome: $(jq -r .backup_name "$META_FILE")"
        echo -e "   - Data: $(jq -r .timestamp "$META_FILE")"
        echo -e "   - Banco: $(jq -r .database "$META_FILE")"
        echo -e "   - Host: $(jq -r .host "$META_FILE")"
        echo -e "   - Tamanho: $(jq -r .size_bytes "$META_FILE" | numfmt --to=iec) bytes"
    else
        echo -e "   ${YELLOW}⚠️  jq não instalado - pulando leitura de metadados${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Arquivo de metadados não encontrado${NC}"
fi
echo ""

# ============================================================================
# TESTE 4: Testar extração do arquivo
# ============================================================================
echo -e "${BLUE}[4/5] Testando extração do arquivo...${NC}"

TEMP_DIR=$(mktemp -d)
trap "rm -rf $TEMP_DIR" EXIT

if tar -tzf "$BACKUP_FILE" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Arquivo tar.gz válido e pode ser extraído${NC}"
    
    # Lista conteúdo
    echo ""
    echo -e "   ${YELLOW}Conteúdo do backup:${NC}"
    tar -tzf "$BACKUP_FILE" | head -20 | sed 's/^/   - /'
    
    TOTAL_FILES=$(tar -tzf "$BACKUP_FILE" | wc -l)
    if [ $TOTAL_FILES -gt 20 ]; then
        echo -e "   ${YELLOW}... e mais $(($TOTAL_FILES - 20)) arquivos${NC}"
    fi
else
    echo -e "${RED}❌ Erro ao tentar extrair o arquivo${NC}"
    echo -e "${RED}   O arquivo tar.gz pode estar corrompido${NC}"
    exit 1
fi
echo ""

# ============================================================================
# TESTE 5: Verificar estrutura esperada
# ============================================================================
echo -e "${BLUE}[5/5] Verificando estrutura do backup...${NC}"

EXPECTED_DIRS=("database" "files" "config")
MISSING_DIRS=()

for dir in "${EXPECTED_DIRS[@]}"; do
    if tar -tzf "$BACKUP_FILE" | grep -q "^$BACKUP_NAME/$dir/"; then
        echo -e "${GREEN}✅ Diretório $dir: OK${NC}"
    else
        echo -e "${RED}❌ Diretório $dir: AUSENTE${NC}"
        MISSING_DIRS+=("$dir")
    fi
done

if [ ${#MISSING_DIRS[@]} -gt 0 ]; then
    echo ""
    echo -e "${YELLOW}⚠️  Alguns diretórios esperados estão ausentes:${NC}"
    for dir in "${MISSING_DIRS[@]}"; do
        echo -e "   - $dir"
    done
fi
echo ""

# ============================================================================
# RESUMO FINAL
# ============================================================================
echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     📋 RESUMO DA VALIDAÇÃO                                     ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

if [ ${#MISSING_DIRS[@]} -eq 0 ]; then
    echo -e "${GREEN}✅ BACKUP VÁLIDO E ÍNTEGRO${NC}"
    echo -e "${GREEN}   Todos os testes passaram com sucesso${NC}"
    echo -e "${GREEN}   O backup pode ser usado para restauração${NC}"
    exit 0
else
    echo -e "${YELLOW}⚠️  BACKUP VÁLIDO COM AVISOS${NC}"
    echo -e "${YELLOW}   O backup está íntegro mas alguns componentes estão ausentes${NC}"
    echo -e "${YELLOW}   Revise a estrutura antes de usar em produção${NC}"
    exit 2
fi
