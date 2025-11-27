# SafeShare

Aplicação PHP simples para gerenciamento de cofres compartilhados, senhas e etiquetas. Inclui fluxo de autenticação, controle de membros por papel e associação de etiquetas às senhas.

## Pré-requisitos
- PHP 8+
- MySQL/MariaDB

## Configuração
1. Crie o banco e as tabelas:
   ```sql
   SOURCE database.sql;
   ```
2. Ajuste credenciais em `config.php` se necessário.
3. Inicie um servidor PHP local na raiz do projeto:
   ```bash
   php -S localhost:8000
   ```
4. Acesse `http://localhost:8000` e registre um usuário para começar.

## Estrutura
- `index.php`: roteador simples que carrega as páginas em `pages/` e controla logout.
- `pages/`: telas de login, registro, cofres, membros e etiquetas.
- `public/style.css`: estilos da interface principal.
