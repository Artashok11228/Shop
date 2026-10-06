<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Digital Products' => ['Mobile Phones', 'Laptops', 'Headphones'],
            'Clothing' => ['T-Shirts', 'Shoes'],
            'Home and Kitchen' => ['Cookware', 'Small Appliances'],
            'Sports' => ['Fitness Equipment', 'Bicycles'],
        ];

        foreach ($categories as $parentName => $childNames) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'parent_id' => null]
            );

            foreach ($childNames as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $parent->id]
                );
            }
        }
    }
}
