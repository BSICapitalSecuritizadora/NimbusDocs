#!/bin/bash

##############################################################################
# Script de manutenção do NimbusDocs
# Executa rotação de logs e limpeza de temp
# Uso: ./bin/scripts/maintenance.sh
##############################################################################

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔧 Iniciando manutenção do NimbusDocs...${NC}"

# 1. Rotação de logs
echo -e "${YELLOW}📋 Rotacionando logs...${NC}"
bash "$SCRIPT_DIR/rotate_logs.sh" "$PROJECT_ROOT/storage/logs" 30

# 2. Limpeza de arquivos temporários
echo -e "${YELLOW}🗑️  Limpando arquivos temporários...${NC}"
if [ -d "$PROJECT_ROOT/storage/uploads" ]; then
    find "$PROJECT_ROOT/storage/uploads" -type f -atime +30 -delete
    echo -e "${GREEN}✓ Uploads antigos removidos${NC}"
fi

# 3. Limpeza do rate limiter
if [ -f "$PROJECT_ROOT/storage/rate_limiter.json" ]; then
    # Remove entradas expiradas
    php -r "
        \$file = '$PROJECT_ROOT/storage/rate_limiter.json';
        if (file_exists(\$file)) {
            \$data = json_decode(file_get_contents(\$file), true) ?? [];
            \$now = time();
            foreach (\$data as \$key => \$record) {
                if (\$record['expires_at'] < \$now) {
                    unset(\$data[\$key]);
                }
            }
            file_put_contents(\$file, json_encode(\$data, JSON_PRETTY_PRINT));
        }
    "
    echo -e "${GREEN}✓ Cache de rate limiter limpo${NC}"
fi

# 4. Vacuum do banco de dados (MySQL)
if [ -f "$PROJECT_ROOT/.env" ]; then
    export $(cat "$PROJECT_ROOT/.env" | grep -v '^#' | xargs)
    
    # Otimiza tabelas
    echo -e "${YELLOW}⚡ Otimizando banco de dados...${NC}"
    mysql -h "${DB_HOST:-127.0.0.1}" -u "${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" \
        -e "OPTIMIZE TABLE \`admin_users\`, \`portal_users\`, \`portal_submissions\`, \`audit_logs\`, \`notification_outbox\`;" 2>/dev/null || true
    echo -e "${GREEN}✓ Banco de dados otimizado${NC}"
fi

echo -e "${GREEN}✓ Manutenção concluída!${NC}"
