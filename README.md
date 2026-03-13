# Laravel + Filament + Inertia React Starter

Laravel 12 starter with Filament CMS (`/admin`), Inertia + React frontend, and daisyUI.

## Requirements

- PHP 8.2+
- Composer
- Bun or Node.js

## Installation

1. Install dependencies.

```bash
composer install
bun install (or npm install)
```

2. Create `.env` and generate an app key.

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database.

For SQLite:

```bash
touch database/database.sqlite
```

Set in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/project/database/database.sqlite
```

4. Run migrations.

```bash
php artisan migrate
```

5. Create a Filament admin user.

```bash
php artisan make:filament-user
```

6. Start development.

```bash
composer run dev
```

## URLs

- Frontend: `http://127.0.0.1:8000`
- Filament CMS: `http://127.0.0.1:8000/admin`

## Routing

- `/` -> homepage from CMS (`PageController@home`)
- `/contact` -> dedicated contact page (`PageController@contact`)
- `/{slug}` -> CMS pages (`PageController@show`)
- API submit endpoint: `POST /api/contact`

Files:

- Web routes: `routes/web.php`
- API routes: `routes/api.php`
- Main page controller: `app/Http/Controllers/PageController.php`
- Contact API controller: `app/Http/Controllers/Api/ContactFormController.php`

## CMS Overview

In Filament, under the `CMS` group, the navigation order is:

1. `Pages`
2. `Menu-items`
3. `Contact`

### Pages

Managed via `PageResource`:

- Title, description (used as meta description)
- Slug, publish/homepage toggles
- Rich content
- Image slider
- Hero section (title, text, image, button)

### Menu-items

Managed via `MenuItemResource`:

- Link to a CMS page
- Internal route selection (currently includes `contact`)
- Manual/external URL
- Order, visibility, open-in-new-tab

### Contact

Managed via `ContactFormSettingResource`:

- Title and intro text
- Dynamic form fields (you can add/remove/reorder)
- Per-field type (`text`, `email`, `textarea`, `tel`)
- Per-field label, key, required flag, placeholder
- Button label
- Success and error toast messages

## Frontend Structure

- React entry: `resources/js/app.tsx`
- Pages: `resources/js/pages`
- Components: `resources/js/components`

Contact page uses daisyUI components for:

- `input`
- `label`
- `textarea`
- `validator`
- `toast`

## Troubleshooting

### No application encryption key has been specified

```bash
cp .env.example .env
php artisan key:generate
```

### no such table: users (or other tables)

```bash
php artisan migrate
```

### Uploaded images under `/storage/...` return 404

```bash
php artisan storage:link
```

Also ensure `.env` contains:

```env
FILESYSTEM_DISK=public
FILESYSTEM_PUBLIC_URL=/storage
```

## Static Analysis and Refactoring

This project includes:

- `PHPStan` + `Larastan` for static analysis
- `Rector` for automated refactoring

Run from project root:

```bash
composer phpstan
composer rector:dry
composer rector
```

What each command does:

- `composer phpstan`: runs static analysis (`vendor/bin/phpstan analyse`)
- `composer rector:dry`: shows proposed Rector changes without modifying files
- `composer rector`: applies Rector changes

Config files:

- `phpstan.neon.dist`
- `rector.php`
