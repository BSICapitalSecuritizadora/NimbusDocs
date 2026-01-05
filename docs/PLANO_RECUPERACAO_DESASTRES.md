# 📋 Plano de Recuperação de Desastres - NimbusDocs

## 📊 Informações Críticas

### RTO e RPO

- **RTO (Recovery Time Objective)**: 4 horas
  - Tempo máximo aceitável para recuperação completa do sistema
  
- **RPO (Recovery Point Objective)**: 24 horas
  - Perda máxima aceitável de dados (backups diários)

### Contatos de Emergência

| Função | Nome | Contato | Disponibilidade |
|--------|------|---------|----------------|
| Administrador Primário | [NOME] | [EMAIL/TELEFONE] | 24/7 |
| Administrador Secundário | [NOME] | [EMAIL/TELEFONE] | Horário comercial |
| Suporte Banco de Dados | [NOME] | [EMAIL/TELEFONE] | 24/7 |
| Suporte Infraestrutura | [NOME] | [EMAIL/TELEFONE] | 24/7 |

---

## 🔥 Cenários de Desastre

### 1. Perda Total de Banco de Dados

**Sintomas:**
- MySQL não inicia
- Dados corrompidos
- Tabelas inacessíveis

**Procedimento de Recuperação:**

```bash
# 1. Identificar último backup válido
cd /path/to/NimbusDocs
ls -lht backups/*.tar.gz | head -5

# 2. Validar backup
./bin/scripts/validate-backup.sh backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz

# 3. Parar aplicação (opcional, mas recomendado)
# Desabilitar acesso web ou parar servidor

# 4. Fazer backup do estado atual (mesmo corrompido)
mysqldump --all-databases > /tmp/emergency_dump_$(date +%Y%m%d_%H%M%S).sql

# 5. Restaurar banco de dados
./bin/scripts/test-restore.sh backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz --full-restore

# 6. Verificar integridade
mysql -u root -p -e "USE nimbusdocs; SHOW TABLES; SELECT COUNT(*) FROM users;"

# 7. Reiniciar aplicação
# Reabilitar acesso web

# 8. Testar funcionalidades críticas
```

**Tempo Estimado:** 1-2 horas

---

### 2. Perda de Arquivos de Storage

**Sintomas:**
- Uploads desapareceram
- Imagens não carregam
- Arquivos PDF ausentes

**Procedimento de Recuperação:**

```bash
# 1. Identificar último backup
./bin/scripts/backup-alert.sh check

# 2. Validar backup
./bin/scripts/validate-backup.sh backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz

# 3. Extrair apenas storage
tar -xzf backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz \
    --wildcards '*/files/storage/*'

# 4. Copiar arquivos
BACKUP_NAME=$(basename backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz .tar.gz)
cp -r $BACKUP_NAME/files/storage/* storage/

# 5. Ajustar permissões
chown -R www-data:www-data storage/
chmod -R 755 storage/

# 6. Verificar
ls -lh storage/uploads/
```

**Tempo Estimado:** 30 minutos - 1 hora

---

### 3. Arquivo .env Corrompido/Perdido

**Sintomas:**
- Erro 500 na aplicação
- Não conecta ao banco
- Mensagens de "undefined config"

**Procedimento de Recuperação:**

```bash
# 1. Recuperar .env do backup
tar -xzf backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz \
    --wildcards '*/config/.env'

# 2. Copiar para projeto
BACKUP_NAME=$(basename backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz .tar.gz)
cp $BACKUP_NAME/config/.env .env

# 3. Ajustar permissões
chmod 600 .env

# 4. Verificar configuração
php -r "require 'vendor/autoload.php'; \$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); \$dotenv->load(); echo 'DB: ' . \$_ENV['DB_DATABASE'] . PHP_EOL;"

# 5. Reiniciar aplicação
```

**Tempo Estimado:** 15 minutos

---

### 4. Servidor Comprometido (Invasão/Malware)

**Sintomas:**
- Arquivos modificados inesperadamente
- Tráfego anormal
- Comandos suspeitos em logs

**Procedimento de Recuperação:**

⚠️ **ATENÇÃO: Este é um cenário crítico que requer isolamento imediato!**

```bash
# 1. ISOLAR SERVIDOR IMEDIATAMENTE
# - Desconectar da rede
# - Desabilitar acesso web
# - Bloquear IPs suspeitos no firewall

# 2. Documentar tudo
mkdir /tmp/forensics_$(date +%Y%m%d_%H%M%S)
ps aux > /tmp/forensics_*/processes.txt
netstat -tupln > /tmp/forensics_*/connections.txt
find / -type f -mtime -1 > /tmp/forensics_*/recent_changes.txt

# 3. Criar backup do estado comprometido (para análise forense)
tar -czf /secure/location/compromised_state_$(date +%Y%m%d_%H%M%S).tar.gz \
    /var/www/html/NimbusDocs \
    /var/log \
    /tmp/forensics_*

# 4. Limpar servidor
rm -rf /var/www/html/NimbusDocs/*

# 5. Restaurar de backup limpo (ANTERIOR à invasão)
# Identificar backup confiável (antes da data de comprometimento)
./bin/scripts/validate-backup.sh backups/nimbusdocs_backup_CLEAN_DATE.tar.gz
./bin/scripts/test-restore.sh backups/nimbusdocs_backup_CLEAN_DATE.tar.gz --full-restore

# 6. TROCAR TODAS AS SENHAS
# - Banco de dados
# - Usuários da aplicação
# - SSH/FTP
# - API keys

# 7. Atualizar sistema
apt update && apt upgrade -y
composer update

# 8. Revisar código por backdoors
grep -r "eval(" src/ public/
grep -r "base64_decode" src/ public/
grep -r "shell_exec" src/ public/

# 9. Reforçar segurança
# - Atualizar .htaccess
# - Configurar fail2ban
# - Habilitar ModSecurity
# - Revisar permissões (chmod 644 para arquivos, 755 para diretórios)

# 10. Monitorar por 72 horas
tail -f storage/logs/*.log
```

**Tempo Estimado:** 4-8 horas (+ análise forense adicional)

---

## 🔄 Procedimentos de Teste

### Teste Mensal de Restore (Obrigatório)

**Quando:** Primeira segunda-feira de cada mês, às 10h

**Procedimento:**

```bash
# 1. Selecionar backup da semana anterior
BACKUP_FILE=$(ls -t backups/*.tar.gz | head -1)

# 2. Executar teste de restore (dry-run)
./bin/scripts/test-restore.sh "$BACKUP_FILE"

# 3. Documentar resultado
echo "=== Teste de Restore $(date) ===" >> docs/restore-tests.log
./bin/scripts/test-restore.sh "$BACKUP_FILE" 2>&1 | tee -a docs/restore-tests.log

# 4. Enviar relatório
# [Incluir saída do teste no relatório mensal]
```

**Critérios de Sucesso:**
- ✅ Checksum válido
- ✅ Extração bem-sucedida
- ✅ Banco de dados restaurável
- ✅ Arquivos íntegros
- ✅ Configuração presente

### Teste Trimestral de Desastre (Recomendado)

**Quando:** Último sábado de março, junho, setembro, dezembro

**Procedimento:**

Simular cenário completo de desastre em ambiente de staging:

1. Criar servidor staging limpo
2. Restaurar backup mais recente
3. Testar todas as funcionalidades
4. Medir tempo de recuperação (RTO)
5. Documentar lições aprendidas

---

## 📊 Checklist de Backup

### Verificação Diária (Automatizada)

```bash
# Adicionar ao crontab:
0 1 * * * /path/to/NimbusDocs/bin/scripts/backup.sh
0 2 * * * /path/to/NimbusDocs/bin/scripts/backup-alert.sh check
```

**O que verificar:**
- [ ] Backup foi criado nas últimas 24h
- [ ] Checksum está presente
- [ ] Tamanho é razoável (> 1MB)
- [ ] Arquivo pode ser extraído
- [ ] Espaço em disco suficiente (> 20GB livres)

### Verificação Semanal (Manual)

**Segunda-feira, 9h:**
- [ ] Revisar logs de backup da semana
- [ ] Verificar integridade de 1 backup aleatório
- [ ] Confirmar rotação de backups antigos
- [ ] Testar download de 1 backup do storage remoto (se aplicável)

### Verificação Mensal (Manual)

**Primeira segunda-feira, 10h:**
- [ ] Executar teste completo de restore (dry-run)
- [ ] Revisar e atualizar este documento
- [ ] Verificar contatos de emergência
- [ ] Testar alertas por email
- [ ] Auditar espaço de armazenamento

---

## 🚨 Alertas Automáticos

### Configuração de Alertas

O sistema monitora automaticamente:

1. **Backup Desatualizado** (> 24h)
   - Severidade: WARNING
   - Ação: Verificar cron job

2. **Backup Corrompido** (checksum inválido)
   - Severidade: CRITICAL
   - Ação: Criar backup imediatamente

3. **Backup Muito Pequeno** (< 1MB)
   - Severidade: CRITICAL
   - Ação: Investigar processo de backup

4. **Espaço em Disco Baixo** (< 10GB)
   - Severidade: WARNING
   - Ação: Limpar backups antigos

### Recebendo Alertas por Email

Editar [config/.env](../config/.env):

```env
# Alertas de Backup
ADMIN_EMAIL=seu-email@empresa.com
SMTP_ENABLED=true
SMTP_HOST=smtp.empresa.com
SMTP_PORT=587
SMTP_USERNAME=alertas@empresa.com
SMTP_PASSWORD=senha_segura
```

Testar:

```bash
./bin/scripts/backup-alert.sh check
```

---

## 🔐 Segurança dos Backups

### Armazenamento

- **Local**: `/backups` (no servidor)
- **Remoto**: [CONFIGURAR] AWS S3 / Azure Blob / Google Drive
- **Retenção**: 
  - Diários: 7 dias
  - Semanais: 4 semanas
  - Mensais: 12 meses

### Criptografia

Para backups criptografados:

```bash
# Criar backup criptografado
gpg --symmetric --cipher-algo AES256 backup.tar.gz

# Restaurar backup criptografado
gpg --decrypt backup.tar.gz.gpg > backup.tar.gz
./bin/scripts/test-restore.sh backup.tar.gz
```

### Controle de Acesso

```bash
# Permissões recomendadas
chmod 700 backups/
chmod 600 backups/*.tar.gz
chown root:root backups/
```

---

## 📚 Procedimentos de Rollback

### Rollback de Código

Se uma atualização causou problemas:

```bash
# 1. Identificar versão anterior estável
git log --oneline -10

# 2. Fazer rollback
git checkout <commit-hash-anterior>

# 3. Atualizar dependências
composer install --no-dev

# 4. Limpar cache
rm -rf storage/cache/*

# 5. Testar aplicação
```

### Rollback de Banco de Dados

Se uma migração causou problemas:

```bash
# 1. Parar aplicação
# [Desabilitar acesso web]

# 2. Fazer backup do estado atual
mysqldump nimbusdocs > /tmp/before_rollback_$(date +%Y%m%d_%H%M%S).sql

# 3. Restaurar backup anterior
./bin/scripts/test-restore.sh backups/nimbusdocs_backup_YYYYMMDD_HHMMSS.tar.gz --full-restore

# 4. Verificar integridade
mysql -u root -p nimbusdocs -e "SELECT COUNT(*) FROM users;"

# 5. Reiniciar aplicação
```

---

## 📝 Registro de Incidentes

### Modelo de Relatório

```
RELATÓRIO DE INCIDENTE - NimbusDocs
====================================

Data/Hora: [YYYY-MM-DD HH:MM]
Severidade: [BAIXA/MÉDIA/ALTA/CRÍTICA]
Tipo: [Banco de Dados / Arquivos / Configuração / Segurança / Outro]

DESCRIÇÃO DO PROBLEMA:
[Descrever o que aconteceu]

SINTOMAS OBSERVADOS:
- [Listar sintomas]

CAUSA RAIZ:
[Identificar causa se conhecida]

AÇÕES TOMADAS:
1. [Listar ações em ordem cronológica]
2. ...

BACKUP UTILIZADO:
[Nome do arquivo de backup]

TEMPO DE RECUPERAÇÃO:
- Início: [HH:MM]
- Fim: [HH:MM]
- Total: [X horas]

DADOS PERDIDOS:
[Descrever perda de dados, se houver]

LIÇÕES APRENDIDAS:
[O que pode ser melhorado]

AÇÕES PREVENTIVAS:
1. [Listar melhorias a implementar]
2. ...

Responsável: [Nome]
Assinatura: ___________________
```

Salvar em: `docs/incidents/YYYYMMDD_descricao.md`

---

## 🎯 Priorização de Recuperação

### Componentes Críticos (Prioridade 1)

1. **Banco de Dados** - CRÍTICO
   - Contém todos os dados de submissões
   - RTO: 1 hora

2. **Arquivo .env** - CRÍTICO
   - Credenciais e configuração
   - RTO: 15 minutos

3. **Storage/Uploads** - ALTO
   - PDFs enviados pelos usuários
   - RTO: 2 horas

### Componentes Importantes (Prioridade 2)

4. **Logs** - MÉDIO
   - Necessários para auditoria
   - RTO: 4 horas

5. **Certificados SSL** - MÉDIO
   - Necessários para Graph API
   - RTO: 4 horas

### Componentes Opcionais (Prioridade 3)

6. **Cache** - BAIXO
   - Pode ser regenerado
   - RTO: 24 horas

---

## ✅ Checklist Pós-Recuperação

Após qualquer recuperação de desastre:

- [ ] Sistema está online e acessível
- [ ] Banco de dados respondendo corretamente
- [ ] Login de usuários funcionando
- [ ] Upload de arquivos funcionando
- [ ] Graph API conectando (envio de emails)
- [ ] Logs sendo gravados normalmente
- [ ] Backups voltaram a funcionar
- [ ] Monitoramento ativo
- [ ] Equipe notificada
- [ ] Incidente documentado
- [ ] Post-mortem agendado (se aplicável)

---

## 📞 Suporte Adicional

### Documentação Relacionada

- [README.md](../README.md) - Guia principal do sistema
- [MONITORAMENTO_AVANCADO.md](MONITORAMENTO_AVANCADO.md) - Sistema de monitoramento
- [bin/scripts/backup.sh](../bin/scripts/backup.sh) - Script de backup
- [bin/scripts/validate-backup.sh](../bin/scripts/validate-backup.sh) - Validação
- [bin/scripts/test-restore.sh](../bin/scripts/test-restore.sh) - Teste de restore

### Comandos Rápidos

```bash
# Status atual do sistema
./bin/scripts/backup-alert.sh check

# Criar backup manual
./bin/scripts/backup.sh

# Validar backup
./bin/scripts/validate-backup.sh backups/arquivo.tar.gz

# Testar restore (dry-run)
./bin/scripts/test-restore.sh backups/arquivo.tar.gz

# Listar backups disponíveis
ls -lht backups/*.tar.gz | head -10

# Ver logs de backup
tail -f storage/logs/backup-alerts.log

# Ver monitoramento
# Acessar: https://seu-dominio/admin/monitoring
```

---

## 📅 Manutenção Deste Documento

- **Última atualização:** 2024-12-18
- **Próxima revisão:** 2025-03-18
- **Responsável:** [NOME DO ADMINISTRADOR]

**Histórico de Mudanças:**
- 2024-12-18: Criação inicial do documento

---

**⚠️ IMPORTANTE:** Este documento deve ser revisado e atualizado trimestralmente ou após qualquer incidente significativo.
