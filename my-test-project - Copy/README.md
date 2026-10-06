# Simple Laravel Shop Catalog

This is phase one: the catalog database, demo seeders, and read-only JSON endpoints. It is built with Laravel 12, ordinary Eloquent models, migrations, factories, and seeders. Start here before building product filters or checkout.

## Run it

For a fresh clone, enter `my-test-project - Copy` inside the repository, then run:

```bash
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The example environment uses SQLite and the local database lives at `database/database.sqlite`. Environment files, dependencies, and SQLite data are generated locally and excluded from Git. Generate the application key only during initial setup; an existing installation already has its key.

The unrelated local `api/` checkout is ignored and is not a project dependency. Laravel's API routes are defined in `routes/api.php`.

Browse:

- `http://127.0.0.1:8000/api/categories`
- `http://127.0.0.1:8000/api/products`
- `http://127.0.0.1:8000/api/products/samsung-a23-mob-001`

Products are paginated: use `/api/products?page=2`. The `/` page remains the Laravel welcome page. There is no storefront, login screen, cart, payment, or filter endpoint in this phase.

To run checks:

```bash
php artisan test
php vendor/bin/pint --test app/Models app/Http/Controllers database tests/Feature bootstrap/app.php routes/api.php
```

Tests use an in-memory SQLite database, separate from your development data. They also verify that seeded catalog migrations can be rolled back and reapplied while foreign-key protection remains enabled.

## Demo data

On an empty database, `php artisan migrate --seed` creates:

- 10 customers: `customer1@example.com` through `customer10@example.com`, password `password`.
- 13 categories: 4 parents and 9 children.
- 15 brands, including Samsung, Xiaomi, Nike, and local brands.
- 36 products: 4 in every child category.
- 16 attribute definitions, 84 shared values, 32 category/attribute links, and 149 product/value links.

Customer names, product descriptions, and stock quantities use Faker. Product names and specifications are fixed so you can practice filters against predictable examples. `AUD-004` has zero stock. Prices are demo USD amounts stored as decimals. Product specifications are fictional sample data, including the requested A23 example; they are not manufacturer specifications.

Run `php artisan db:seed` again whenever you need to restore the demo catalog. Seeders match records by email, slug, or SKU rather than assuming IDs. They update the seeded catalog and synchronize its specifications, so counts stay stable. Existing customer passwords and extra products are preserved. Stock and descriptions are regenerated. Only a fresh database is guaranteed to contain exactly 10 users.

## Understand the database

| Table | Purpose | Example |
| --- | --- | --- |
| `users` | Laravel's existing customer accounts | Customer 1 |
| `categories` | Categories with an optional `parent_id` | Mobile Phones belongs to Digital Products |
| `brands` | Shared manufacturer names | Samsung |
| `products` | Core product data and its category/brand | A23, price, stock, SKU |
| `product_attributes` | Names of specifications and their rules | RAM, Color, Size |
| `attribute_values` | Reusable choices for each attribute | RAM 16 GB, Color Blue |
| `category_product_attribute` | Attributes applicable to a category | Phones have RAM; shirts have Size |
| `attribute_value_product` | Values selected for a product | A23 has RAM 16 and Color Blue |

The relationship for a specification is:

`Product -> attribute_value_product -> AttributeValue -> ProductAttribute`

The relationship for a category is:

`Digital Products -> Mobile Phones -> Samsung A23`

Each product belongs to one category and optionally one brand. Categories can be nested further through `parent_id`. Assign products and attribute definitions to the relevant leaf category. Parent categories organize navigation; they do not automatically inherit attributes or include descendant products in `products()`.

For the two requested examples:

```text
Samsung A23 (MOB-001)
  Digital Products / Mobile Phones
  color: blue
  ram: 16 GB
  storage: 128 GB
  rear-camera: 13 MP

Sport T-Shirt Code 13631 (TSH-13631)
  Clothing / T-Shirts
  color: green
  size: L, XL, XXL
  material: polyester
```

Each size is a separate value and a separate product link. Sizes are not stored as comma-separated strings or JSON. This makes them easy to JOIN later.

## Read and write with simple relationships

Try these in `php artisan tinker`:

```php
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;

$phone = Product::with('category.parent', 'brand', 'attributeValues.attribute')
    ->where('sku', 'MOB-001')
    ->firstOrFail();

foreach ($phone->attributeValues as $value) {
    echo $value->attribute->name.': '.$value->label.PHP_EOL;
}

$shirt = Product::where('sku', 'TSH-13631')->firstOrFail();
$size = ProductAttribute::where('slug', 'size')->firstOrFail();
$sizeIds = $size->values()->whereIn('value', ['l', 'xl', 'xxl'])->pluck('id');

// Replace only the shirt's size links; preserve its color and material.
$oldSizeIds = $shirt->attributeValues()
    ->where('attribute_values.product_attribute_id', $size->id)
    ->pluck('attribute_values.id');

// In application code, wrap a replace operation like this in DB::transaction().
$shirt->attributeValues()->detach($oldSizeIds);
$shirt->attributeValues()->syncWithoutDetaching($sizeIds);
```

`sync($allValueIds)` replaces ALL specifications on a product. Use it only when you have the full list, as the product seeder does.

## Add an attribute without changing the products table

For example, give shirts a Fit specification:

```php
use App\Models\Category;
use App\Models\ProductAttribute;

$fit = ProductAttribute::firstOrCreate(
    ['slug' => 'fit'],
    ['name' => 'Fit', 'is_numeric' => false, 'allows_multiple' => false]
);

$regular = $fit->values()->firstOrCreate(
    ['value' => 'regular'],
    ['label' => 'Regular']
);

$shirts = Category::where('slug', 't-shirts')->firstOrFail();
$shirts->attributes()->syncWithoutDetaching([$fit->id]);
```

Then link `$regular->id` to a shirt's `attributeValues()`. If this is part of the permanent demo catalog, also update both relevant seeders; category seeding restores its declared attribute list.

## Next phase: filters using JOINs

The schema already has indexes on attribute/value lookups, numeric values, both directions of the pivot tables, and category/active/price. There is no generic filter engine to learn yet.

This example finds mobile phones with a blue OR green color AND at least 16 GB of RAM. The same query is covered by a feature test:

```php
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;

$mobile = Category::where('slug', 'mobile-phones')->firstOrFail();
$color = ProductAttribute::where('slug', 'color')->firstOrFail();
$ram = ProductAttribute::where('slug', 'ram')->firstOrFail();

$products = Product::query()
    ->join('attribute_value_product as colors', 'products.id', '=', 'colors.product_id')
    ->join('attribute_values as color_values', 'colors.attribute_value_id', '=', 'color_values.id')
    ->join('attribute_value_product as memory', 'products.id', '=', 'memory.product_id')
    ->join('attribute_values as ram_values', 'memory.attribute_value_id', '=', 'ram_values.id')
    ->where('products.category_id', $mobile->id)
    ->where('products.is_active', true)
    ->where('color_values.product_attribute_id', $color->id)
    ->whereIn('color_values.value', ['blue', 'green'])
    ->where('ram_values.product_attribute_id', $ram->id)
    ->where('ram_values.numeric_value', '>=', 16)
    ->select('products.*')
    ->distinct()
    ->get();
```

This returns the seeded A23. Separate aliases allow different specifications to match different rows. `distinct()` avoids duplicate products when several selected values match, such as both L and XL. Always qualify joined columns, for example `products.id` instead of `id`.

`attribute_values.value` is the canonical value for exact matching (`blue`, `16`, `xl`). `label` is display text (`Blue`, `16 GB`, `XL`). `numeric_value` is a decimal for range filtering; comparing text values would sort `128` and `16` incorrectly. Store numeric attributes in one consistent unit, defined on the attribute.

For brand, price, and availability you can filter the ordinary product columns directly. When filtering a parent category, collect descendant category IDs first and use `whereIn('products.category_id', $ids)`.

## Rules for future forms

Foreign keys prevent broken links. Unique keys prevent duplicate SKUs, slugs, options within an attribute, and product/value pairs. Deleting a product removes its links but keeps the shared value dictionary. Deleting a used category is blocked. Deleting a brand leaves its products with a null brand.

When adding create/edit forms, validate these business rules before saving:

- A category must not become its own ancestor.
- A product's attributes must belong to its category's allowed attribute list.
- Each selected value must belong to the intended attribute.
- Attributes with `allows_multiple = false` must have at most one selected value.
- Numeric attributes require a numeric value in the declared unit; text attributes use a null numeric value.
- Price and stock must be nonnegative, and slugs/SKUs must be unique.

These are application validation rules rather than complicated database triggers. The product seeder validates the category and multiple-value rules for its data. The feature tests check the complete seeded relationships.

Stock and price belong to the product for this phase. If different size/color combinations need their own SKU, stock, or price later, introduce `product_variants` and link options to each variant. Multiple current size values describe available choices; they do not track inventory per size. Orders, carts, reviews, sellers, and payments can be added separately when needed.

Use ordinary controllers and Form Requests for the next phase. There is no repository/service layer or extra package required for this catalog.

Reference: [Laravel 12 Eloquent relationships](https://laravel.com/docs/12.x/eloquent-relationships).
