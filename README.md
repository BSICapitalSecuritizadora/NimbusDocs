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
