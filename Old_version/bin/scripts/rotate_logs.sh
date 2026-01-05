#!/bin/bash

##############################################################################
# Script de rotação de logs do NimbusDocs
# Rotaciona logs antigos e compacta
# Uso: ./bin/scripts/rotate_logs.sh [log_dir] [days]
##############################################################################

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
LOG_DIR="${1:-$PROJECT_ROOT/storage/logs}"
DAYS="${2:-30}"

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔄 Iniciando rotação de logs...${NC}"

# Verifica se diretório existe
if [ ! -d "$LOG_DIR" ]; then
    echo -e "${RED}✗ Diretório de logs não encontrado: $LOG_DIR${NC}"
    exit 1
fi

# Cria subdiretório de backup
mkdir -p "$LOG_DIR/archive"

# Rotaciona arquivos .log
ROTATED=0
for logfile in "$LOG_DIR"/*.log; do
    if [ -f "$logfile" ]; then
        # Verifica idade do arquivo
        if [ $(find "$logfile" -mtime +$DAYS) ]; then
            BASENAME=$(basename "$logfile" .log)
            TIMESTAMP=$(date +%Y%m%d_%H%M%S)
            ARCHIVENAME="${BASENAME}_${TIMESTAMP}.log.gz"
            
            # Compacta e move para archive
            gzip -c "$logfile" > "$LOG_DIR/archive/$ARCHIVENAME"
            > "$logfile"  # Limpa o arquivo original
            
            echo -e "${GREEN}✓ Arquivado: $BASENAME${NC}"
            ROTATED=$((ROTATED + 1))
        fi
    fi
done

# Remove arquivos de mais de 90 dias
REMOVED=0
for archivefile in "$LOG_DIR/archive"/*.gz; do
    if [ -f "$archivefile" ]; then
        if [ $(find "$archivefile" -mtime +90) ]; then
            rm "$archivefile"
            echo -e "${GREEN}✓ Removido: $(basename "$archivefile")${NC}"
            REMOVED=$((REMOVED + 1))
        fi
    fi
done

# Resumo
echo -e "${YELLOW}📊 Resumo da rotação:${NC}"
echo -e "  ${GREEN}Arquivos rotacionados: $ROTATED${NC}"
echo -e "  ${GREEN}Arquivos removidos: $REMOVED${NC}"
echo -e "${GREEN}✓ Rotação concluída!${NC}"
