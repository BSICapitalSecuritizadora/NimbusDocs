#!/bin/bash

##############################################################################
# Script de backup do NimbusDocs
# Faz backup do banco de dados e arquivos
# Uso: ./bin/scripts/backup.sh [backup_dir]
##############################################################################

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
BACKUP_DIR="${1:-.}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="nimbusdocs_backup_${TIMESTAMP}"

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🔄 Iniciando backup do NimbusDocs...${NC}"

# Cria diretório de backup
mkdir -p "$BACKUP_DIR/$BACKUP_NAME"
mkdir -p "$BACKUP_DIR/$BACKUP_NAME/database"
mkdir -p "$BACKUP_DIR/$BACKUP_NAME/files"
mkdir -p "$BACKUP_DIR/$BACKUP_NAME/config"

# Carrega variáveis de ambiente
if [ -f "$PROJECT_ROOT/.env" ]; then
    export $(cat "$PROJECT_ROOT/.env" | grep -v '^#' | xargs)
else
    echo -e "${RED}✗ Arquivo .env não encontrado${NC}"
    exit 1
fi

# Backup do banco de dados
echo -e "${YELLOW}📊 Fazendo backup do banco de dados...${NC}"
MYSQL_DUMP="mysqldump -h ${DB_HOST:-127.0.0.1} -u ${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}"
if eval "$MYSQL_DUMP > $BACKUP_DIR/$BACKUP_NAME/database/nimbusdocs.sql"; then
    echo -e "${GREEN}✓ Banco de dados: OK${NC}"
else
    echo -e "${RED}✗ Falha ao fazer backup do banco${NC}"
    exit 1
fi

# Backup de arquivos
echo -e "${YELLOW}📁 Fazendo backup de arquivos...${NC}"
if [ -d "$PROJECT_ROOT/storage" ]; then
    cp -r "$PROJECT_ROOT/storage" "$BACKUP_DIR/$BACKUP_NAME/files/" 2>/dev/null || true
    echo -e "${GREEN}✓ Arquivos storage: OK${NC}"
fi

# Backup de configuração
echo -e "${YELLOW}⚙️  Fazendo backup de configuração...${NC}"
cp "$PROJECT_ROOT/.env" "$BACKUP_DIR/$BACKUP_NAME/config/.env" 2>/dev/null || true
cp "$PROJECT_ROOT/config/config.php" "$BACKUP_DIR/$BACKUP_NAME/config/" 2>/dev/null || true
echo -e "${GREEN}✓ Configurações: OK${NC}"

# Cria arquivo de informações
cat > "$BACKUP_DIR/$BACKUP_NAME/INFO.txt" <<EOF
Backup do NimbusDocs
====================
Data: $(date)
Banco: ${DB_DATABASE}
Host: ${DB_HOST}

Conteúdo:
- database/nimbusdocs.sql: Dump do banco de dados
- files/storage: Arquivos de usuário (uploads, logs, etc)
- config/.env: Variáveis de ambiente
- config/config.php: Configuração da aplicação

Para restaurar:
1. mysql -h localhost -u root -p ${DB_DATABASE} < database/nimbusdocs.sql
2. cp -r files/storage/* /caminho/do/projeto/storage/
3. cp config/.env /caminho/do/projeto/
EOF

# Compacta o backup
echo -e "${YELLOW}📦 Compactando backup...${NC}"
cd "$BACKUP_DIR"
tar -czf "${BACKUP_NAME}.tar.gz" "$BACKUP_NAME"
rm -rf "$BACKUP_NAME"

echo -e "${GREEN}✓ Backup concluído com sucesso!${NC}"
echo -e "${GREEN}📁 Localização: $BACKUP_DIR/${BACKUP_NAME}.tar.gz${NC}"
echo -e "${GREEN}📊 Tamanho: $(du -h "$BACKUP_DIR/${BACKUP_NAME}.tar.gz" | cut -f1)${NC}"
