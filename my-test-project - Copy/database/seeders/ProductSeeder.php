<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use LogicException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // These are fictional demo specifications, not manufacturer data.
        $products = [
            [
                'name' => 'Samsung A23', 'sku' => 'MOB-001', 'category' => 'mobile-phones', 'brand' => 'samsung', 'price' => 220,
                'attributes' => ['color' => 'blue', 'ram' => '16', 'storage' => '128', 'rear-camera' => '13', 'screen-size' => '6.6', 'battery' => '5000'],
            ],
            [
                'name' => 'Samsung A54', 'sku' => 'MOB-002', 'category' => 'mobile-phones', 'brand' => 'samsung', 'price' => 350,
                'attributes' => ['color' => 'black', 'ram' => '8', 'storage' => '256', 'rear-camera' => '50', 'screen-size' => '6.5', 'battery' => '5000'],
            ],
            [
                'name' => 'Xiaomi Redmi Note', 'sku' => 'MOB-003', 'category' => 'mobile-phones', 'brand' => 'xiaomi', 'price' => 180,
                'attributes' => ['color' => 'blue', 'ram' => '8', 'storage' => '128', 'rear-camera' => '108', 'screen-size' => '6.6', 'battery' => '5000'],
            ],
            [
                'name' => 'Apple iPhone Demo', 'sku' => 'MOB-004', 'category' => 'mobile-phones', 'brand' => 'apple', 'price' => 750,
                'attributes' => ['color' => 'white', 'ram' => '6', 'storage' => '256', 'rear-camera' => '48', 'screen-size' => '6.1', 'battery' => '4000'],
            ],
            [
                'name' => 'Lenovo Everyday Laptop', 'sku' => 'LAP-001', 'category' => 'laptops', 'brand' => 'lenovo', 'price' => 550,
                'attributes' => ['color' => 'gray', 'ram' => '8', 'storage' => '512', 'screen-size' => '15.6', 'processor' => 'intel-i5'],
            ],
            [
                'name' => 'Asus Gaming Laptop', 'sku' => 'LAP-002', 'category' => 'laptops', 'brand' => 'asus', 'price' => 1200,
                'attributes' => ['color' => 'black', 'ram' => '16', 'storage' => '1024', 'screen-size' => '15.6', 'processor' => 'intel-i7'],
            ],
            [
                'name' => 'Apple Lightweight Laptop', 'sku' => 'LAP-003', 'category' => 'laptops', 'brand' => 'apple', 'price' => 1400,
                'attributes' => ['color' => 'silver', 'ram' => '16', 'storage' => '512', 'screen-size' => '13.3', 'processor' => 'apple-m2'],
            ],
            [
                'name' => 'Lenovo Student Laptop', 'sku' => 'LAP-004', 'category' => 'laptops', 'brand' => 'lenovo', 'price' => 650,
                'attributes' => ['color' => 'blue', 'ram' => '16', 'storage' => '256', 'screen-size' => '14', 'processor' => 'ryzen-5'],
            ],
            [
                'name' => 'Sony Wireless Headphones', 'sku' => 'AUD-001', 'category' => 'headphones', 'brand' => 'sony', 'price' => 160,
                'attributes' => ['color' => 'black', 'connectivity' => 'bluetooth'],
            ],
            [
                'name' => 'JBL Everyday Headphones', 'sku' => 'AUD-002', 'category' => 'headphones', 'brand' => 'jbl', 'price' => 55,
                'attributes' => ['color' => 'blue', 'connectivity' => 'wired'],
            ],
            [
                'name' => 'Samsung Wireless Earbuds', 'sku' => 'AUD-003', 'category' => 'headphones', 'brand' => 'samsung', 'price' => 95,
                'attributes' => ['color' => 'white', 'connectivity' => 'bluetooth'],
            ],
            [
                'name' => 'JBL Sport Headphones', 'sku' => 'AUD-004', 'category' => 'headphones', 'brand' => 'jbl', 'price' => 80,
                'attributes' => ['color' => 'red', 'connectivity' => 'bluetooth'],
            ],
            [
                'name' => 'Sport T-Shirt Code 13631', 'sku' => 'TSH-13631', 'category' => 't-shirts', 'brand' => 'local-wear', 'price' => 18,
                'attributes' => ['color' => 'green', 'size' => ['l', 'xl', 'xxl'], 'material' => 'polyester'],
            ],
            [
                'name' => 'Nike Cotton T-Shirt', 'sku' => 'TSH-002', 'category' => 't-shirts', 'brand' => 'nike', 'price' => 30,
                'attributes' => ['color' => 'black', 'size' => ['s', 'm', 'l'], 'material' => 'cotton'],
            ],
            [
                'name' => 'Adidas Training T-Shirt', 'sku' => 'TSH-003', 'category' => 't-shirts', 'brand' => 'adidas', 'price' => 28,
                'attributes' => ['color' => 'blue', 'size' => ['m', 'l', 'xl'], 'material' => 'polyester'],
            ],
            [
                'name' => 'Everyday White T-Shirt', 'sku' => 'TSH-004', 'category' => 't-shirts', 'brand' => 'local-wear', 'price' => 12,
                'attributes' => ['color' => 'white', 'size' => ['s', 'm', 'l', 'xl', 'xxl'], 'material' => 'cotton'],
            ],
            [
                'name' => 'Nike Running Shoes', 'sku' => 'SHO-001', 'category' => 'shoes', 'brand' => 'nike', 'price' => 95,
                'attributes' => ['color' => 'black', 'shoe-size' => ['40', '41', '42', '43'], 'material' => 'mesh'],
            ],
            [
                'name' => 'Adidas Walking Shoes', 'sku' => 'SHO-002', 'category' => 'shoes', 'brand' => 'adidas', 'price' => 85,
                'attributes' => ['color' => 'white', 'shoe-size' => ['38', '39', '40', '41'], 'material' => 'leather'],
            ],
            [
                'name' => 'Local Everyday Shoes', 'sku' => 'SHO-003', 'category' => 'shoes', 'brand' => 'local-wear', 'price' => 35,
                'attributes' => ['color' => 'gray', 'shoe-size' => ['41', '42', '43', '44'], 'material' => 'mesh'],
            ],
            [
                'name' => 'Decathlon Trail Shoes', 'sku' => 'SHO-004', 'category' => 'shoes', 'brand' => 'decathlon', 'price' => 65,
                'attributes' => ['color' => 'green', 'shoe-size' => ['40', '42', '44'], 'material' => 'polyester'],
            ],
            [
                'name' => 'Tefal Family Cooking Pot', 'sku' => 'COO-001', 'category' => 'cookware', 'brand' => 'tefal', 'price' => 45,
                'attributes' => ['color' => 'silver', 'capacity' => '5', 'material' => 'steel'],
            ],
            [
                'name' => 'Tefal Small Saucepan', 'sku' => 'COO-002', 'category' => 'cookware', 'brand' => 'tefal', 'price' => 25,
                'attributes' => ['color' => 'black', 'capacity' => '2', 'material' => 'aluminum'],
            ],
            [
                'name' => 'Family Steel Cooking Pot', 'sku' => 'COO-003', 'category' => 'cookware', 'brand' => null, 'price' => 30,
                'attributes' => ['color' => 'silver', 'capacity' => '3', 'material' => 'steel'],
            ],
            [
                'name' => 'Compact Camping Pot', 'sku' => 'COO-004', 'category' => 'cookware', 'brand' => 'decathlon', 'price' => 20,
                'attributes' => ['color' => 'gray', 'capacity' => '1', 'material' => 'aluminum'],
            ],
            [
                'name' => 'Philips Electric Kettle', 'sku' => 'APP-001', 'category' => 'small-appliances', 'brand' => 'philips', 'price' => 40,
                'attributes' => ['color' => 'white', 'capacity' => '1.7', 'power' => '2000'],
            ],
            [
                'name' => 'Bosch Kitchen Blender', 'sku' => 'APP-002', 'category' => 'small-appliances', 'brand' => 'bosch', 'price' => 65,
                'attributes' => ['color' => 'black', 'capacity' => '2', 'power' => '800'],
            ],
            [
                'name' => 'Philips Compact Blender', 'sku' => 'APP-003', 'category' => 'small-appliances', 'brand' => 'philips', 'price' => 50,
                'attributes' => ['color' => 'silver', 'capacity' => '1', 'power' => '500'],
            ],
            [
                'name' => 'Tefal Family Air Fryer', 'sku' => 'APP-004', 'category' => 'small-appliances', 'brand' => 'tefal', 'price' => 110,
                'attributes' => ['color' => 'black', 'capacity' => '5', 'power' => '1500'],
            ],
            [
                'name' => 'Decathlon Dumbbell 5 kg', 'sku' => 'FIT-001', 'category' => 'fitness-equipment', 'brand' => 'decathlon', 'price' => 22,
                'attributes' => ['color' => 'black', 'weight' => '5', 'material' => 'steel'],
            ],
            [
                'name' => 'Decathlon Dumbbell 10 kg', 'sku' => 'FIT-002', 'category' => 'fitness-equipment', 'brand' => 'decathlon', 'price' => 38,
                'attributes' => ['color' => 'black', 'weight' => '10', 'material' => 'steel'],
            ],
            [
                'name' => 'Lightweight Yoga Mat', 'sku' => 'FIT-003', 'category' => 'fitness-equipment', 'brand' => null, 'price' => 15,
                'attributes' => ['color' => 'green', 'weight' => '0.5', 'material' => 'rubber'],
            ],
            [
                'name' => 'Adidas Training Weight', 'sku' => 'FIT-004', 'category' => 'fitness-equipment', 'brand' => 'adidas', 'price' => 18,
                'attributes' => ['color' => 'red', 'weight' => '2', 'material' => 'steel'],
            ],
            [
                'name' => 'Giant Mountain Bicycle', 'sku' => 'BIK-001', 'category' => 'bicycles', 'brand' => 'giant', 'price' => 550,
                'attributes' => ['color' => 'blue', 'wheel-size' => '29', 'gears' => '24', 'material' => 'aluminum'],
            ],
            [
                'name' => 'Decathlon City Bicycle', 'sku' => 'BIK-002', 'category' => 'bicycles', 'brand' => 'decathlon', 'price' => 300,
                'attributes' => ['color' => 'green', 'wheel-size' => '26', 'gears' => '7', 'material' => 'steel'],
            ],
            [
                'name' => 'Giant Trail Bicycle', 'sku' => 'BIK-003', 'category' => 'bicycles', 'brand' => 'giant', 'price' => 420,
                'attributes' => ['color' => 'black', 'wheel-size' => '27.5', 'gears' => '21', 'material' => 'aluminum'],
            ],
            [
                'name' => 'Decathlon Kids Bicycle', 'sku' => 'BIK-004', 'category' => 'bicycles', 'brand' => 'decathlon', 'price' => 150,
                'attributes' => ['color' => 'red', 'wheel-size' => '20', 'gears' => '1', 'material' => 'steel'],
            ],
        ];

        // Load these once rather than doing a lookup for every specification.
        $categories = Category::with('attributes')->get()->keyBy('slug');
        $brands = Brand::all()->keyBy('slug');
        $values = AttributeValue::with('attribute')->get()->keyBy(function ($value) {
            return $value->attribute->slug.':'.$value->value;
        });

        foreach ($products as $data) {
            $category = $categories[$data['category']];
            $valueIds = [];

            foreach ($data['attributes'] as $attributeSlug => $selectedValues) {
                $attribute = $category->attributes->firstWhere('slug', $attributeSlug);
                $selectedValues = (array) $selectedValues;

                if (! $attribute || (! $attribute->allows_multiple && count($selectedValues) !== 1)) {
                    throw new LogicException('Invalid attribute for '.$data['sku'].': '.$attributeSlug);
                }

                foreach ($selectedValues as $selectedValue) {
                    $key = $attributeSlug.':'.$selectedValue;

                    if (! $values->has($key)) {
                        throw new LogicException('Unknown attribute value: '.$key);
                    }

                    $valueIds[] = $values[$key]->id;
                }
            }

            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $category->id,
                    'brand_id' => $data['brand'] ? $brands[$data['brand']]->id : null,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name'].'-'.$data['sku']),
                    'description' => 'Demo catalog item. '.fake()->sentence(),
                    // One demo currency: USD. Use your chosen currency consistently later.
                    'price' => $data['price'],
                    'stock' => fake()->numberBetween(1, 80),
                    'is_active' => true,
                ]
            );

            $product->attributeValues()->sync($valueIds);
        }

        // Include an unavailable item for later stock filters.
        Product::where('sku', 'AUD-004')->update(['stock' => 0]);
    }
}
