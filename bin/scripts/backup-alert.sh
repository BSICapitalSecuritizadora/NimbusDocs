#!/bin/bash

##############################################################################
# Sistema de Alertas de Backup - NimbusDocs
# Monitora backups e envia alertas em caso de falha
# Uso: ./bin/scripts/backup-alert.sh [check|monitor]
##############################################################################

set -e

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuração
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"
BACKUP_DIR="${PROJECT_ROOT}/backups"
LOG_FILE="${PROJECT_ROOT}/storage/logs/backup-alerts.log"
STATUS_FILE="${PROJECT_ROOT}/storage/logs/backup-status.json"

# Limites de tempo
MAX_BACKUP_AGE_HOURS=24
MAX_NO_BACKUP_DAYS=7

# Configuração de email (ajuste conforme necessário)
ALERT_EMAIL="${ADMIN_EMAIL:-admin@nimbusdocs.local}"
SMTP_ENABLED="${SMTP_ENABLED:-false}"

# ============================================================================
# Funções auxiliares
# ============================================================================

log_alert() {
    local level=$1
    shift
    local message="$@"
    
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] [$level] $message" >> "$LOG_FILE"
    
    if [ "$level" = "ERROR" ]; then
        echo -e "${RED}❌ $message${NC}"
    elif [ "$level" = "WARNING" ]; then
        echo -e "${YELLOW}⚠️  $message${NC}"
    elif [ "$level" = "INFO" ]; then
        echo -e "${GREEN}✅ $message${NC}"
    fi
}

send_email_alert() {
    local subject="$1"
    local body="$2"
    
    if [ "$SMTP_ENABLED" = "true" ]; then
        # Implementar envio via SMTP (requer configuração)
        echo "$body" | mail -s "$subject" "$ALERT_EMAIL" 2>/dev/null || {
            log_alert "WARNING" "Falha ao enviar email para $ALERT_EMAIL"
        }
    else
        log_alert "INFO" "Email alert: $subject (SMTP disabled)"
    fi
}

update_status() {
    local status=$1
    local message=$2
    local backup_file=$3
    
    cat > "$STATUS_FILE" <<EOF
{
    "last_check": "$(date -u +"%Y-%m-%dT%H:%M:%SZ")",
    "status": "$status",
    "message": "$message",
    "last_backup": "$backup_file",
    "alert_sent": $([ "$status" = "ERROR" ] && echo "true" || echo "false")
}
EOF
}

# ============================================================================
# Verificar último backup
# ============================================================================

check_last_backup() {
    echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║     🔍 VERIFICAÇÃO DE BACKUP - NimbusDocs                     ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    
    # Cria diretório de logs se não existir
    mkdir -p "$(dirname "$LOG_FILE")"
    mkdir -p "$(dirname "$STATUS_FILE")"
    
    # Verifica se diretório de backup existe
    if [ ! -d "$BACKUP_DIR" ]; then
        log_alert "ERROR" "Diretório de backup não existe: $BACKUP_DIR"
        update_status "ERROR" "Backup directory missing" ""
        send_email_alert "❌ NimbusDocs - Diretório de Backup Ausente" \
            "O diretório de backup não foi encontrado: $BACKUP_DIR"
        return 1
    fi
    
    # Encontra último backup
    LAST_BACKUP=$(find "$BACKUP_DIR" -name "*.tar.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -rn | head -1 | cut -d' ' -f2-)
    
    if [ -z "$LAST_BACKUP" ]; then
        log_alert "ERROR" "Nenhum backup encontrado em $BACKUP_DIR"
        update_status "ERROR" "No backups found" ""
        send_email_alert "❌ NimbusDocs - Nenhum Backup Encontrado" \
            "Não há backups disponíveis no diretório: $BACKUP_DIR\n\nAção necessária: Execute o script de backup imediatamente."
        return 1
    fi
    
    echo -e "Último backup: ${YELLOW}$(basename "$LAST_BACKUP")${NC}"
    echo ""
    
    # Verifica idade do backup
    BACKUP_TIME=$(stat -c %Y "$LAST_BACKUP" 2>/dev/null || stat -f %m "$LAST_BACKUP" 2>/dev/null)
    CURRENT_TIME=$(date +%s)
    AGE_HOURS=$(( ($CURRENT_TIME - $BACKUP_TIME) / 3600 ))
    
    echo -e "${BLUE}[1/4] Verificando idade do backup...${NC}"
    echo -e "   Idade: ${YELLOW}${AGE_HOURS}h${NC}"
    
    if [ $AGE_HOURS -gt $MAX_BACKUP_AGE_HOURS ]; then
        log_alert "WARNING" "Backup está desatualizado ($AGE_HOURS horas)"
        update_status "WARNING" "Backup too old ($AGE_HOURS hours)" "$LAST_BACKUP"
        send_email_alert "⚠️  NimbusDocs - Backup Desatualizado" \
            "O último backup tem $AGE_HOURS horas.\n\nLimite: $MAX_BACKUP_AGE_HOURS horas\n\nBackup: $(basename "$LAST_BACKUP")\n\nAção: Verifique se os backups automáticos estão funcionando."
        echo -e "${YELLOW}⚠️  Backup está desatualizado (> ${MAX_BACKUP_AGE_HOURS}h)${NC}"
    else
        echo -e "${GREEN}✅ Backup está recente (< ${MAX_BACKUP_AGE_HOURS}h)${NC}"
    fi
    echo ""
    
    # Verifica integridade do checksum
    echo -e "${BLUE}[2/4] Verificando integridade do backup...${NC}"
    
    CHECKSUM_FILE="${LAST_BACKUP}.sha256"
    
    if [ -f "$CHECKSUM_FILE" ]; then
        EXPECTED_CHECKSUM=$(awk '{print $1}' "$CHECKSUM_FILE")
        CALCULATED_CHECKSUM=$(sha256sum "$LAST_BACKUP" | awk '{print $1}')
        
        if [ "$EXPECTED_CHECKSUM" = "$CALCULATED_CHECKSUM" ]; then
            echo -e "${GREEN}✅ Checksum válido${NC}"
            log_alert "INFO" "Checksum verificado: OK"
        else
            log_alert "ERROR" "Checksum inválido para $LAST_BACKUP"
            update_status "ERROR" "Corrupted backup (checksum mismatch)" "$LAST_BACKUP"
            send_email_alert "❌ NimbusDocs - Backup Corrompido" \
                "O backup está corrompido (checksum inválido)!\n\nBackup: $(basename "$LAST_BACKUP")\n\nEsperado: $EXPECTED_CHECKSUM\nCalculado: $CALCULATED_CHECKSUM\n\nAção URGENTE: Criar novo backup imediatamente!"
            echo -e "${RED}❌ Checksum inválido - BACKUP CORROMPIDO!${NC}"
            return 1
        fi
    else
        echo -e "${YELLOW}⚠️  Checksum não encontrado${NC}"
        log_alert "WARNING" "Checksum file missing for $LAST_BACKUP"
    fi
    echo ""
    
    # Verifica tamanho do backup
    echo -e "${BLUE}[3/4] Verificando tamanho do backup...${NC}"
    
    BACKUP_SIZE=$(du -h "$LAST_BACKUP" | cut -f1)
    BACKUP_SIZE_BYTES=$(stat -c %s "$LAST_BACKUP" 2>/dev/null || stat -f %z "$LAST_BACKUP" 2>/dev/null)
    
    echo -e "   Tamanho: ${YELLOW}${BACKUP_SIZE}${NC}"
    
    # Alerta se backup for muito pequeno (< 1MB)
    MIN_SIZE_BYTES=1048576 # 1MB
    if [ $BACKUP_SIZE_BYTES -lt $MIN_SIZE_BYTES ]; then
        log_alert "ERROR" "Backup suspeito: tamanho muito pequeno ($BACKUP_SIZE)"
        update_status "ERROR" "Backup too small ($BACKUP_SIZE)" "$LAST_BACKUP"
        send_email_alert "❌ NimbusDocs - Backup Suspeito" \
            "O backup parece incompleto (tamanho muito pequeno).\n\nTamanho: $BACKUP_SIZE\n\nBackup: $(basename "$LAST_BACKUP")\n\nAção: Verificar processo de backup."
        echo -e "${RED}❌ Backup muito pequeno (< 1MB)${NC}"
        return 1
    else
        echo -e "${GREEN}✅ Tamanho OK${NC}"
    fi
    echo ""
    
    # Testa extração
    echo -e "${BLUE}[4/4] Testando extração do backup...${NC}"
    
    if tar -tzf "$LAST_BACKUP" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Backup pode ser extraído${NC}"
        log_alert "INFO" "Backup validation successful"
    else
        log_alert "ERROR" "Falha ao testar extração de $LAST_BACKUP"
        update_status "ERROR" "Cannot extract backup" "$LAST_BACKUP"
        send_email_alert "❌ NimbusDocs - Backup Corrompido" \
            "Não foi possível extrair o backup!\n\nBackup: $(basename "$LAST_BACKUP")\n\nAção URGENTE: Criar novo backup."
        echo -e "${RED}❌ Falha ao testar extração${NC}"
        return 1
    fi
    echo ""
    
    # Sucesso
    update_status "OK" "All checks passed" "$LAST_BACKUP"
    log_alert "INFO" "Backup check completed successfully"
    
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║     ✅ TODOS OS TESTES PASSARAM                                ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
    
    return 0
}

# ============================================================================
# Monitoramento contínuo
# ============================================================================

monitor_backups() {
    echo -e "${BLUE}Iniciando monitoramento contínuo de backups...${NC}"
    echo -e "${YELLOW}(Pressione Ctrl+C para parar)${NC}"
    echo ""
    
    while true; do
        check_last_backup
        echo ""
        echo -e "${YELLOW}Próxima verificação em 1 hora...${NC}"
        sleep 3600 # 1 hora
    done
}

# ============================================================================
# Main
# ============================================================================

case "${1:-check}" in
    check)
        check_last_backup
        exit $?
        ;;
    monitor)
        monitor_backups
        ;;
    *)
        echo "Uso: $0 [check|monitor]"
        echo ""
        echo "Comandos:"
        echo "  check      Verifica o último backup (padrão)"
        echo "  monitor    Monitoramento contínuo (a cada 1h)"
        exit 1
        ;;
esac
