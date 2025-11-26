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
  - Autenticação segura (login/senha pré-cadastrados).
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

2. Rode as migrações para criar todas as tabelas necessárias (admins, usuários finais, tokens, submissões, arquivos, notas e auditoria):

   ```bash
   composer migrate
   ```

3. Popule dados iniciais (credenciais padrão podem ser alteradas via variáveis `SEED_*`):

   ```bash
   composer seed
   ```

   - Admin padrão: `admin@example.com` / `Admin@123`
   - Usuário final padrão: `cliente@example.com` / `Cliente@123`

O Portal do Usuário agora autentica **via login/senha** (email ou documento + senha) e, opcionalmente, aceita códigos de acesso gerados pelo administrador.
