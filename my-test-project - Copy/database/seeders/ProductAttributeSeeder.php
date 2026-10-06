<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Values are stored without units; labels are for display.
        $attributes = [
            'color' => ['name' => 'Color', 'values' => ['blue', 'green', 'black', 'white', 'red', 'gray', 'silver']],
            'ram' => ['name' => 'RAM', 'unit' => 'GB', 'values' => ['4', '6', '8', '12', '16', '32']],
            'storage' => ['name' => 'Storage', 'unit' => 'GB', 'values' => ['64', '128', '256', '512', '1024']],
            'rear-camera' => ['name' => 'Rear Camera', 'unit' => 'MP', 'values' => ['12', '13', '48', '50', '108']],
            'screen-size' => ['name' => 'Screen Size', 'unit' => 'inch', 'values' => ['6.1', '6.5', '6.6', '13.3', '14', '15.6']],
            'processor' => ['name' => 'Processor', 'values' => ['intel-i5', 'intel-i7', 'ryzen-5', 'apple-m2']],
            'battery' => ['name' => 'Battery', 'unit' => 'mAh', 'values' => ['3000', '4000', '5000']],
            'size' => ['name' => 'Size', 'allows_multiple' => true, 'values' => ['s', 'm', 'l', 'xl', 'xxl']],
            'shoe-size' => ['name' => 'Shoe Size', 'allows_multiple' => true, 'unit' => 'EU', 'values' => ['38', '39', '40', '41', '42', '43', '44']],
            'material' => ['name' => 'Material', 'values' => ['cotton', 'polyester', 'leather', 'mesh', 'steel', 'aluminum', 'plastic', 'rubber']],
            'connectivity' => ['name' => 'Connectivity', 'values' => ['bluetooth', 'wired']],
            'weight' => ['name' => 'Weight', 'unit' => 'kg', 'values' => ['0.5', '1', '2', '5', '10', '12', '15']],
            'capacity' => ['name' => 'Capacity', 'unit' => 'L', 'values' => ['1', '1.7', '2', '3', '5']],
            'power' => ['name' => 'Power', 'unit' => 'W', 'values' => ['500', '800', '1000', '1500', '2000']],
            'wheel-size' => ['name' => 'Wheel Size', 'unit' => 'inch', 'values' => ['20', '24', '26', '27.5', '29']],
            'gears' => ['name' => 'Gears', 'unit' => 'speeds', 'values' => ['1', '7', '21', '24']],
        ];

        foreach ($attributes as $slug => $data) {
            $unit = $data['unit'] ?? null;
            $attribute = ProductAttribute::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'unit' => $unit,
                    'is_numeric' => $unit !== null,
                    'allows_multiple' => $data['allows_multiple'] ?? false,
                    'is_filterable' => true,
                ]
            );

            foreach ($data['values'] as $value) {
                $attribute->values()->updateOrCreate(
                    ['value' => $value],
                    [
                        'label' => $unit ? $value.' '.$unit : ($slug === 'size' ? strtoupper($value) : Str::headline($value)),
                        'numeric_value' => $attribute->is_numeric ? $value : null,
                    ]
                );
            }
        }

        // A category defines which attributes make sense for its products.
        $categoryAttributes = [
            'mobile-phones' => ['color', 'ram', 'storage', 'rear-camera', 'screen-size', 'battery'],
            'laptops' => ['color', 'ram', 'storage', 'screen-size', 'processor'],
            'headphones' => ['color', 'connectivity'],
            't-shirts' => ['color', 'size', 'material'],
            'shoes' => ['color', 'shoe-size', 'material'],
            'cookware' => ['color', 'capacity', 'material'],
            'small-appliances' => ['color', 'capacity', 'power'],
            'fitness-equipment' => ['color', 'weight', 'material'],
            'bicycles' => ['color', 'wheel-size', 'gears', 'material'],
        ];

        foreach ($categoryAttributes as $categorySlug => $attributeSlugs) {
            $category = Category::where('slug', $categorySlug)->firstOrFail();
            $attributeIds = ProductAttribute::whereIn('slug', $attributeSlugs)->pluck('id');
            $category->attributes()->sync($attributeIds);
        }
    }
}
