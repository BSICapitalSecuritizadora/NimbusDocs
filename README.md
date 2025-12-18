# NimbusDocs

NimbusDocs é um portal web seguro e modular para o **envio e gerenciamento centralizado de informações e documentos**, fazendo parte do ecossistema **Nimbus**.

O sistema é dividido em dois módulos principais:

- **Módulo Administrativo (Back-Office)**  
  Interface de gerenciamento utilizada por administradores, responsável por:
  - Gestão de administradores e usuários finais (CRUD completo).
  - Visualização global de todas as submissões.
  - Download de documentos e consulta de logs de auditoria.

- **Módulo de Usuário (Portal do Usuário)**
  Interface utilizada pelo usuário final para:
  - Autenticação segura via **código de acesso** gerado pelo administrador, com validade controlada.
  - Envio de informações via formulários.
  - Upload de arquivos (PDF, XLS/XLSX, DOCX, etc.).
  - Consulta ao histórico de envios próprios.

## Stack Tecnológica

- **Back-end:** PHP 8+
- **Banco de dados:** MySQL
- **Front-end:** Bootstrap 5.3+ (via CDN)
- **Gerenciador de dependências:** Composer (autoload PSR-4 obrigatório)
- **Padrões de código:**
  - PSR-12 (coding style)
  - PSR-4 (autoloading)

O foco do NimbusDocs é garantir **segurança**, **segregação de responsabilidades** entre perfis (administradores x usuários finais) e **rastreabilidade** completa por meio de logs de auditoria.

## Preparação do ambiente (DB e seeds)

1. Instale dependências:

   ```bash
   composer install
   ```

2. Copie o exemplo de variáveis de ambiente e ajuste conforme necessário:

  ```bash
  cp .env.example .env
  ```

  - Ajuste credenciais de DB, Graph/Mail e toggles do rescue da outbox:
    - `OUTBOX_RESCUE_MINUTES` (padrão 30)
    - `OUTBOX_RESCUE_LOG` (padrão true)
    - `OUTBOX_DUPLICATE_WINDOW_HOURS` (padrão 24) — janela de tempo para evitar notificações duplicadas
    - opcional fallback: `NOTIFICATION_WORKER_RESCUE_MINUTES`

  Sugestão para produção:

  ```env
  APP_ENV=production
  APP_DEBUG=false
  LOG_LEVEL=info
  OUTBOX_RESCUE_LOG=false
  OUTBOX_DUPLICATE_WINDOW_HOURS=24
  # mantenha OUTBOX_RESCUE_MINUTES ajustado conforme sua tolerância, ex.: 30
  ```

3. Rode as migrações para criar todas as tabelas necessárias (admins, usuários finais, tokens, submissões, arquivos, notas e auditoria):

   ```bash
   composer migrate
   ```

4. Popule dados iniciais (credenciais padrão podem ser alteradas via variáveis `SEED_*`):

   ```bash
   composer seed
   ```

   - Admin padrão: `admin@example.com` / `Admin@123`
   - Usuário final padrão: `cliente@example.com` (o administrador deve gerar ou usar o código seed exibido ao rodar o comando acima)

O Portal do Usuário autentica **exclusivamente por código de acesso** gerado no módulo administrativo, com validade configurável (1h, 24h ou 7d). Cada código é de uso único e pode ser revogado automaticamente ao gerar um novo.

## Worker de notificações (fila de e-mail)

- O worker que processa a tabela `notification_outbox` está em `bin/notifications-worker.php`.
- O "rescue" de jobs travados em `SENDING` acontece dentro do repositório (`MySqlNotificationOutboxRepository::claimBatch()`), garantindo o mesmo comportamento para qualquer consumidor.
- A janela para esse rescue é configurável via `.env`:

```env
# Quantos minutos um job em SENDING deve ter para ser resgatado
OUTBOX_RESCUE_MINUTES=30
# Alternativa suportada (fallback):
# NOTIFICATION_WORKER_RESCUE_MINUTES=30

# Habilita/desabilita logs do rescue (default: true)
OUTBOX_RESCUE_LOG=true

# Janela de tempo (em horas) para evitar notificações duplicadas
# Impede reenvio da mesma notificação ao mesmo usuário dentro deste período
OUTBOX_DUPLICATE_WINDOW_HOURS=24
```

Se as variáveis não estiverem definidas, os padrões são **30 minutos** para rescue e **24 horas** para janela de duplicação.
