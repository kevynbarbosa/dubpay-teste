## Instalacao

### Sem Docker

1. Adentre o diretorio do projeto.
2. Execute:

```
composer install
cp .env.example .env
php artisan serve
```

### Com Docker (Sail)

1. Adentre o diretorio do projeto.
2. Execute:

```
docker run --rm \
 -u "$(id -u):$(id -g)" \
 -v $(pwd):/var/www/html \
 -w /var/www/html \
 laravelsail/php82-composer:latest \
 composer install --ignore-platform-reqs

cp .env.example .env
docker compose up -d
```

Alternativa:

```
./vendor/bin/sail up
```

Observacao: para iniciar mais rapido, deixei a `APP_KEY` em `.env.example`, apesar de ser uma prática nao recomendada.

### Banco de dados

No Docker usamos MySQL. Se preferir SQLite, ajuste o `.env` para:

```
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/database.sqlite
DB_HOST=
DB_PORT=
DB_USERNAME=
DB_PASSWORD=
```

E crie o arquivo do banco:

```
touch database/database.sqlite
```

## Rotas da API

Base: `http://localhost:8000/api`

### Postman

Use a colecao na raiz do projeto: `DubPayTeste.postman_collection.json`.

### Auth

-   `POST /auth/register` registra um novo usuario e retorna token.
-   `POST /auth/login` autentica o usuario e retorna token.
-   `GET /auth/me` retorna os dados do usuario autenticado (Bearer token).
-   `POST /auth/logout` invalida o token atual (Bearer token).

### Usuario

-   `GET /user` retorna o usuario autenticado (Protegida pelo middleware de autenticação).

### Pagamentos

-   `POST /pay` cria uma transacao de pagamento (Protegida pelo middleware de autenticação).

### Webhooks

-   `POST /handle-provider-a-webhook` recebe atualizacoes do Provider A.
-   `POST /handle-provider-b-webhook` recebe atualizacoes do Provider B.
