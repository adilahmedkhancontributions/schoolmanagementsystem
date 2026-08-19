# School Management System

A multi-role School Management System built with Laravel 11, Livewire 4,
Tailwind CSS 3, Alpine.js 3, and Spatie Laravel Permission. See
[PROGRESS.md](PROGRESS.md) for the full feature roadmap and current status.

Roles: Super Admin, School Admin, Teacher, Student, Parent.

## Requirements

- PHP 8.2+
- Composer 2
- Node.js 18+ and npm
- MySQL 8 (or SQLite for local development)

## First-time setup

```bash
# 1. Install PHP and JS dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
# SQLite (fastest for local dev):
touch database/database.sqlite
# and in .env set: DB_CONNECTION=sqlite   (comment out DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD)
#
# MySQL: create a database and set DB_* in .env accordingly, then:
php artisan migrate --seed

# 4. Build frontend assets
npm run build
```

## Running locally

```bash
php artisan serve
npm run dev   # in a separate terminal, for hot-reloading during development
```

Visit `http://localhost:8000` and log in at `/login`.

## Demo accounts

Seeded by `database/seeders/DemoDataSeeder.php`. Password for all: `password`.

| Role | Email |
|---|---|
| Super Admin | superadmin@example.com |
| School Admin | admin@demoschool.test |
| Teacher | teacher@demoschool.test |
| Student | student@demoschool.test |
| Parent | parent@demoschool.test |

## Useful commands

```bash
php artisan migrate:fresh --seed   # reset the database and reseed demo data
php artisan route:list             # inspect registered routes
php artisan pint                   # code style check/fix (PSR-12)
php artisan test                   # run the test suite
```

## Project structure notes

- `app/Livewire/` — Livewire 4 components (dashboards, CRUD modules), organized by role/module.
- `app/Support/Navigation.php` — single source of truth for sidebar/bottom-nav items per role.
- `resources/views/layouts/dashboard.blade.php` — shared responsive shell (sidebar, topbar, mobile bottom nav) used by all authenticated pages.
- `database/seeders/RolePermissionSeeder.php` — defines roles and permissions.
- `database/seeders/DemoDataSeeder.php` — creates one demo school with a user per role.

## Deployment (shared hosting)

Not yet documented — tracked in `PROGRESS.md` under Phase 5 (Polish & Deployment).
