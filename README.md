# Priprave.net

Priprave.net is a Laravel 12 app for browsing, publishing, and managing school materials.

## TODO

- [ ] Captcha for registration
- [ ] Check comment button (not updating)

## Tech Stack

- PHP 8.4
- Laravel 12
- Livewire 4 + Flux UI
- Filament 5 (admin)
- MySQL
- Meilisearch (Laravel Scout)
- Vite + Tailwind CSS v4

## Prerequisites

- PHP 8.4+
- Composer
- Node.js + npm
- MySQL 8+
- Docker (for Meilisearch)

## Local Setup

1. Install dependencies:

```bash
composer install
npm ci
```

2. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Update `.env` database values (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4. Run migrations:

```bash
php artisan migrate
```

5. Build frontend assets:

```bash
npm run build
```

## Run Application

Run backend + queue worker + logs + Vite together:

```bash
composer run dev
```

Or run only the HTTP server:

```bash
php artisan serve
```

## Meilisearch (Required for Search)

Start Meilisearch:

```bash
docker compose -f docker/compose.yml up -d
```

Then sync index settings and import documents:

```bash
php artisan scout:sync-index-settings
php artisan scout:import "App\\Models\\Document"
```

If needed, confirm `.env` has:

```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
```

## Optional: Social Login Setup

To enable Google/Facebook login, set these in `.env`:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

## Testing

Run all tests:

```bash
php artisan test --compact
```

Run a single test file:

```bash
php artisan test --compact tests/Feature/ProfileTest.php
```

## Useful Commands

```bash
# Format PHP files
vendor/bin/pint --dirty --format agent

# Clear configuration cache
php artisan config:clear

# Rebuild search index for documents
php artisan scout:flush "App\\Models\\Document"
php artisan scout:import "App\\Models\\Document"
```

## Main Routes

- `/` home
- `/brskanje` browse documents
- `/dodajanje` create document (auth + verified)
- `/profil` profile (auth + verified)
- `/prijava` login
- `/prijava/registracija` registration
