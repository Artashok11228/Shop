# Shop

A simple Laravel 12 shop catalog for practicing categories, product specifications, and JOIN filters.

The Laravel application is in [`my-test-project - Copy`](<my-test-project - Copy/>). See its [database guide](<my-test-project - Copy/README.md>) for the schema, demo data, and JOIN examples.

## First-time setup

PHP 8.2 or newer, Composer, and the PDO SQLite extension are required. From the cloned repository:

```bash
cd "my-test-project - Copy"
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

These setup commands use PHP so they work in Git Bash, PowerShell, and Linux/macOS terminals. Generate the application key once during initial setup.

Visit:

- `http://127.0.0.1:8000/api/products`
- `http://127.0.0.1:8000/api/categories`

The database seeders create 10 demo customers, 36 products, 13 categories, and filterable specifications. The home page is the Laravel welcome page; storefront and checkout development are separate phases.

## Development

Work on `main`, inside the Laravel application directory:

```bash
git pull
php artisan test
git add .
git commit -m "Describe your change"
git push
```

The shop's `.gitignore` excludes local environment files, dependencies, SQLite databases, and the unrelated local `api` checkout. The root `.gitignore` excludes sibling practice folders. No submodule is required to run this project.
