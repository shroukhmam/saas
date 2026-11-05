# SaaS E-commerce

A multi-tenant e-commerce platform built on Laravel 11. Each store (tenant) gets its own PostgreSQL schema, its own admin panel, and a mobile API.

## Stack

| Part | Choice |
|---|---|
| Framework | Laravel 11 (PHP 8.2) |
| Multi-tenancy | [stancl/tenancy](https://tenancyforlaravel.com) v3, schema per tenant |
| Database | PostgreSQL (`saas_landlord`) |
| Cache | Redis 7.4 via `predis/predis` (needed for tenant cache tags) |
| Central admin auth | Laravel Breeze (Blade) |
| API auth | Laravel Sanctum (tokens live in the tenant schema) |

## Architecture

```
saas_landlord (PostgreSQL)
├── public              ← central tables: users, tenants, domains, plans, jobs, cache
├── tenant_<id>         ← one schema per store: users, products, orders, tokens, ...
└── tenant_<id>
```

- **Central app** (`routes/web.php`): the landlord admin panel under `/admin`. It runs on the central domains (`localhost`, `127.0.0.1`, `CENTRAL_DOMAIN`).
- **Tenant app** (`routes/tenant.php`): each store's admin panel. `InitializeTenancyByDomain` picks the tenant from the domain.
- **Mobile API** (`routes/api.php`): `/api/v1/...`, identified by subdomain or the `X-Tenant` header.

Tenancy bootstrappers switch the database schema, cache tags, storage path and queue context when a tenant is active.

### Migrations

| Folder | Runs on | Command |
|---|---|---|
| `database/migrations` | central `public` schema | `php artisan migrate` |
| `database/migrations-tenant` | every tenant schema | `php artisan tenants:migrate` |

Never put a tenant migration in `database/migrations`. It will run on the central schema.

## Local setup

Requirements: PHP 8.2 with `pdo_pgsql`, Composer, Node.js, PostgreSQL, Docker.

1. Install the dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
2. Copy the environment file and make an app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Create the PostgreSQL database and user that `.env` names (`saas_landlord` / `saas_user` by default).
4. Start Redis:
   ```bash
   docker compose up -d redis
   ```
5. Run the central migrations and create the central admin:
   ```bash
   php artisan migrate
   php artisan db:seed --class=CentralAdminSeeder
   ```
6. Start the app:
   ```bash
   php artisan serve
   ```
7. Open http://localhost:8000/admin/login and sign in as `admin@saas.local` / `password`.

> **Warning:** The seeded password is for local development only. Change it on any shared or public server.

### Run everything in Docker

`compose.yaml` has an `app` service (PHP 8.2, `php artisan serve` on port 8000) and a `redis` service. The `app` service uses the host network, so it reaches PostgreSQL on `127.0.0.1:5432`.

```bash
docker compose up -d
```

## Folder layout

```
app/
├── Http/Controllers/{Central,Tenant,Api}
├── Http/Middleware/{Central,Tenant}
├── Http/Requests/{Central,Tenant,Api}
├── Models/Central        ← Tenant, Domain, Plan (central connection)
├── Models/Tenant         ← Product, Order, Customer (tenant connection)
├── Services              ← TenantProvisioningService, OrderService, InventoryService
└── Actions/{Central,Tenant}
database/
├── migrations            ← central only
└── migrations-tenant     ← tenant only
routes/
├── web.php               ← central admin
├── tenant.php            ← tenant admin
└── api.php               ← mobile API
```

## Progress

| Phase | Focus | Status |
|---|---|---|
| 0 | Laravel + tenancy + Breeze setup | Done |
| 1 | Central and tenant migrations | In progress: central tables partly done, tenant tables not started |
| 2 | Tenant provisioning | Not started |
| 3 | Central admin (tenants, plans) | Login done, management not started |
| 4 | Tenant auth | Not started |
| 5 | Tenant panel (products, orders, settings) | Not started |
| 6 | Mobile API | Not started |
| 7 | Queues, storage, caching, events | Caching done (Redis + tags), the rest partly done |
| 8 | Tests | Not started |

## Common pitfalls

- Do not query tenant models before tenancy starts. The query goes to the central schema without an error.
- `personal_access_tokens` must stay in the tenant schema, or tokens from all tenants mix together.
- Queued jobs must start tenancy again in the worker. `QueueTenancyBootstrapper` does this for jobs that you dispatch inside a tenant.
- Deleting a tenant drops its schema (`TenantDeleted` → `DeleteDatabase`). Back up first.
- Redis must run. With `CACHE_STORE=redis`, the app fails on any cache call when Redis is down.
