<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Samsung', 'Xiaomi', 'Apple', 'Lenovo', 'Asus', 'Sony',
            'JBL', 'Nike', 'Adidas', 'Local Wear', 'Tefal', 'Philips',
            'Bosch', 'Decathlon', 'Giant',
        ];

        foreach ($brands as $name) {
            Brand::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
