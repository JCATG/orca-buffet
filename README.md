# Orca Buffet

Sistema de orçamento para buffet. O cliente monta o pedido no site. A equipe recebe esse pedido no painel, ajusta os valores e envia o orçamento pelo WhatsApp, com link e PDF.

## Requisitos

- PHP 8.3 ou superior
- Composer
- Node.js e npm
- MySQL
- Docker, só se for guardar as fotos no MinIO

## Configurar

Na pasta do projeto:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

### Banco de dados

Crie o banco e o usuário no MySQL. Troque a senha pela que quiser usar:

```sql
CREATE DATABASE orca_buffet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'orca_buffet'@'127.0.0.1' IDENTIFIED BY 'sua_senha';
GRANT ALL PRIVILEGES ON orca_buffet.* TO 'orca_buffet'@'127.0.0.1';
FLUSH PRIVILEGES;
```

No `.env`, confira estes valores:

```env
APP_NAME="Sabor & Festa"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orca_buffet
DB_USERNAME=orca_buffet
DB_PASSWORD=sua_senha
```

`APP_NAME` aparece no site, no painel e no PDF.

Depois rode as migrations e crie o usuário do painel:

```bash
php artisan migrate
php artisan db:seed
```

O seeder usa o e-mail definido em `database/seeders/DatabaseSeeder.php`. A senha padrão é `password`. Não existe tela de cadastro: o acesso ao painel é só por esse usuário, ou por outro criado direto no banco.

### Fotos dos itens

O jeito mais simples é salvar as fotos no próprio projeto. No `.env`:

```env
MEDIA_DISK=public
```

E crie o link público do storage:

```bash
php artisan storage:link
```

As fotos ficam em `storage/app/public` e são servidas em `/storage`.

Para usar o MinIO, veja a seção abaixo e deixe `MEDIA_DISK=s3`.

### Imagem da página inicial

Se existir o arquivo `public/images/hero.jpg`, ele aparece na tela inicial. Sem esse arquivo, a página usa um fundo verde.

## Rodar

Em um terminal:

```bash
php artisan serve
```

O site abre em [http://127.0.0.1:8000](http://127.0.0.1:8000).

| Endereço | O que é |
| --- | --- |
| `/` | Página inicial |
| `/montar-orcamento` | Pedido do cliente |
| `/login` | Entrada da equipe |
| `/admin/orcamentos` | Pedidos |
| `/admin/products` | Itens do cardápio |
| `/admin/categories` | Categorias |
| `/admin/customers` | Clientes |
| `/admin/users` | Funcionários |
| `/admin/configs` | Configurações |

Se mudar CSS ou JavaScript e a tela ficar sem estilo, rode de novo `npm run build`. Se estiver usando `composer run dev` ou `npm run dev` e sobrar o arquivo `public/hot`, apague esse arquivo antes de servir só com `php artisan serve`.

## MinIO (opcional)

O `docker-compose.yml` sobe o MinIO nas portas **9002** (API) e **9003** (console). A porta 9000 fica livre de propósito, porque muitas máquinas já usam ela (por exemplo, o Portainer).

```bash
docker compose up -d
```

Console: [http://127.0.0.1:9003](http://127.0.0.1:9003)

- Usuário: `minio`
- Senha: `minio12345`

Crie o bucket `orca-buffet` e deixe a leitura pública (Access / Anonymous, política de download). Sem isso, a foto sobe, mas não aparece no site.

No `.env`:

```env
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=minio
AWS_SECRET_ACCESS_KEY=minio12345
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=orca-buffet
AWS_ENDPOINT=http://127.0.0.1:9002
AWS_URL=http://127.0.0.1:9002/orca-buffet
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Depois de mudar o `.env`:

```bash
php artisan config:clear
```

Se o upload de foto falhar, volte para `MEDIA_DISK=public` e rode `php artisan storage:link`.

## Páginas com Livewire

Cada tela é um único arquivo: a classe PHP fica no topo e o HTML logo abaixo. A rota aponta para esse arquivo com `Route::livewire`; o nome `pages::admin.relatorios.index` corresponde a `resources/views/pages/admin/relatorios/⚡index.blade.php`.

```bash
php artisan make:livewire pages::admin.relatorios.index
```

Em `routes/admin.php`, dentro do grupo `admin`:

```php
Route::livewire('relatorios', 'pages::admin.relatorios.index')->name('relatorios.index');
```

O arquivo gerado fica assim:

```php
<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Relatórios')] class extends Component
{
    //
};
?>

<div>
    <h1>Relatórios</h1>
</div>
```

A página abre em `/admin/relatorios`. O painel já usa o layout com a sidebar; não precisa criar controller nem view separada.

## O que não versionar

O arquivo `.env` fica fora do Git. Ele tem senha do banco e chave da aplicação. Use o `.env.example` como modelo.
