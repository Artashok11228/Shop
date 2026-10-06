<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_seeders_create_a_complete_catalog(): void
    {
        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseCount('categories', 13);
        $this->assertDatabaseCount('brands', 15);
        $this->assertDatabaseCount('products', 36);
        $this->assertDatabaseCount('product_attributes', 16);
        $this->assertDatabaseCount('attribute_values', 84);
        $this->assertDatabaseCount('category_product_attribute', 32);
        $this->assertDatabaseCount('attribute_value_product', 149);
        $this->assertTrue(Hash::check('password', User::first()->password));

        foreach (Product::with('category.attributes', 'attributeValues.attribute')->get() as $product) {
            $allowedAttributes = $product->category->attributes;
            $this->assertNotEmpty($product->attributeValues);
            $this->assertCount(4, $product->category->products);

            foreach ($product->attributeValues as $value) {
                $this->assertTrue($allowedAttributes->contains('id', $value->product_attribute_id));
                $this->assertSame($value->attribute->is_numeric, $value->numeric_value !== null);
            }

            foreach ($allowedAttributes as $attribute) {
                $selected = $product->attributeValues->where('product_attribute_id', $attribute->id);
                $this->assertNotEmpty($selected);

                if (! $attribute->allows_multiple) {
                    $this->assertCount(1, $selected);
                }
            }
        }
    }

    public function test_reseeding_does_not_duplicate_records_or_overwrite_customer_passwords(): void
    {
        $customer = User::where('email', 'customer1@example.com')->firstOrFail();
        $customer->update(['password' => 'changed-password']);
        $extraProduct = Product::factory()->create();
        $tables = [
            'users', 'categories', 'brands', 'products', 'product_attributes',
            'attribute_values', 'category_product_attribute', 'attribute_value_product',
        ];
        $counts = [];

        foreach ($tables as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        $this->seed();

        foreach ($counts as $table => $count) {
            $this->assertDatabaseCount($table, $count);
        }

        $this->assertTrue(Hash::check('changed-password', $customer->fresh()->password));
        $this->assertModelExists($extraProduct);
    }

    public function test_example_phone_has_the_requested_specifications_and_category_tree(): void
    {
        $phone = Product::with('category.parent', 'attributeValues.attribute')->where('sku', 'MOB-001')->firstOrFail();
        $specifications = $phone->attributeValues->mapWithKeys(function ($value) {
            return [$value->attribute->slug => $value->value];
        });

        $this->assertSame('digital-products', $phone->category->parent->slug);
        $this->assertSame('mobile-phones', $phone->category->slug);
        $this->assertSame('blue', $specifications['color']);
        $this->assertSame('16', $specifications['ram']);
        $this->assertSame('128', $specifications['storage']);
        $this->assertSame('13', $specifications['rear-camera']);
        $this->assertSame('220.00', $phone->price);
    }

    public function test_shirt_supports_multiple_sizes(): void
    {
        $shirt = Product::where('sku', 'TSH-13631')->firstOrFail();
        $sizes = $shirt->attributeValues()->whereHas('attribute', function ($query) {
            $query->where('slug', 'size');
        })->pluck('value')->all();

        $this->assertEqualsCanonicalizing(['l', 'xl', 'xxl'], $sizes);
    }

    public function test_joins_combine_category_color_and_numeric_ram_filters(): void
    {
        $mobile = Category::where('slug', 'mobile-phones')->firstOrFail();
        $color = ProductAttribute::where('slug', 'color')->firstOrFail();
        $ram = ProductAttribute::where('slug', 'ram')->firstOrFail();

        // OR within colors, AND between color and RAM; RAM is compared numerically.
        $products = Product::query()
            ->join('attribute_value_product as colors', 'products.id', '=', 'colors.product_id')
            ->join('attribute_values as color_values', 'colors.attribute_value_id', '=', 'color_values.id')
            ->join('attribute_value_product as memory', 'products.id', '=', 'memory.product_id')
            ->join('attribute_values as ram_values', 'memory.attribute_value_id', '=', 'ram_values.id')
            ->where('products.category_id', $mobile->id)
            ->where('color_values.product_attribute_id', $color->id)
            ->whereIn('color_values.value', ['blue', 'green'])
            ->where('ram_values.product_attribute_id', $ram->id)
            ->where('ram_values.numeric_value', '>=', 16)
            ->select('products.*')
            ->distinct()
            ->get();

        $this->assertSame(['MOB-001'], $products->pluck('sku')->all());
    }

    public function test_joined_multi_size_matches_return_each_product_once(): void
    {
        $size = ProductAttribute::where('slug', 'size')->firstOrFail();
        $products = Product::query()
            ->join('attribute_value_product', 'products.id', '=', 'attribute_value_product.product_id')
            ->join('attribute_values', 'attribute_value_product.attribute_value_id', '=', 'attribute_values.id')
            ->where('attribute_values.product_attribute_id', $size->id)
            ->whereIn('attribute_values.value', ['l', 'xl'])
            ->select('products.*')
            ->distinct()
            ->get();

        $this->assertCount(4, $products);
        $this->assertCount(1, $products->where('sku', 'TSH-13631'));
    }

    public function test_product_value_links_cannot_be_duplicated(): void
    {
        $phone = Product::where('sku', 'MOB-001')->firstOrFail();
        $value = $phone->attributeValues()->firstOrFail();

        $this->expectException(QueryException::class);
        $phone->attributeValues()->attach($value->id);
    }

    public function test_product_value_links_require_an_existing_value(): void
    {
        $phone = Product::where('sku', 'MOB-001')->firstOrFail();

        $this->expectException(QueryException::class);
        $phone->attributeValues()->attach(999999);
    }

    public function test_deleting_a_product_removes_its_links_but_keeps_shared_values(): void
    {
        $phone = Product::where('sku', 'MOB-001')->firstOrFail();
        $value = $phone->attributeValues()->firstOrFail();
        $phone->delete();

        $this->assertDatabaseMissing('attribute_value_product', ['product_id' => $phone->id]);
        $this->assertModelExists($value);
        $this->assertDatabaseCount('products', 35);
    }

    public function test_deleting_a_brand_keeps_its_products(): void
    {
        Brand::where('slug', 'samsung')->firstOrFail()->delete();

        $this->assertDatabaseCount('products', 36);
        $this->assertNull(Product::where('sku', 'MOB-001')->firstOrFail()->brand_id);
    }

    public function test_used_categories_cannot_be_deleted(): void
    {
        $this->expectException(QueryException::class);
        Category::where('slug', 'mobile-phones')->firstOrFail()->delete();
    }

    public function test_catalog_endpoints_include_relations_and_pagination(): void
    {
        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonPath('total', 36)
            ->assertJsonCount(12, 'data')
            ->assertJsonStructure(['data' => [['category' => ['parent'], 'brand', 'attribute_values' => [['attribute']]]]]);

        $phone = Product::where('sku', 'MOB-001')->firstOrFail();
        $this->getJson('/api/products/'.$phone->slug)
            ->assertOk()
            ->assertJsonPath('sku', 'MOB-001')
            ->assertJsonPath('category.parent.slug', 'digital-products');

        $this->getJson('/api/categories')->assertOk()->assertJsonCount(13);
        $this->getJson('/api/products/missing-product')->assertNotFound();
    }

    public function test_inactive_products_are_hidden_from_the_catalog(): void
    {
        $phone = Product::where('sku', 'MOB-001')->firstOrFail();
        $phone->update(['is_active' => false]);

        $this->getJson('/api/products')->assertOk()->assertJsonPath('total', 35);
        $this->getJson('/api/products/'.$phone->slug)->assertNotFound();
    }
}
