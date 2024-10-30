# Nome do Projeto

Um aplicativo de dicionário que permite pesquisar palavras, adicionar às favoritas e acompanhar o histórico de pesquisa.

## Tecnologias Usadas

- **Linguagem:** PHP
- **Framework:** Laravel
- **Banco de Dados:** MariaDB
- **Cache:** Redis
- **Autenticação:** JWT

## Como Instalar e Usar

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/seu-usuario/seu-repositorio.git
   ```

2. **Acesse o diretório do projeto:**

   ```bash
   cd seu-repositorio
   ```

3. **Instale as dependências do projeto:**

   ```bash
   composer install
   ```

4. **Configure o arquivo `.env`:**

   Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente, incluindo as credenciais do banco de dados e as configurações do Redis.

   ```bash
   cp .env.example .env
   ```

5. **Gere a chave de aplicativo:**

   ```bash
   php artisan key:generate
   ```

6. **Migre o banco de dados:**

   ```bash
   php artisan migrate
   ```

7. **Inicie o servidor:**

   ```bash
   php artisan serve
   ```

8. **Acesse a API:**

   Abra seu navegador ou ferramenta de API (como Postman) e acesse `http://localhost:8000`.

## .gitignore

Certifique-se de que o arquivo `.gitignore` esteja presente e configurado corretamente para ignorar arquivos desnecessários, como:

```
/vendor
/.env
/node_modules
```

---

**Este é um desafio da Coodesh.**
