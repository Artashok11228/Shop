<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        return Product::with('category.parent', 'brand', 'attributeValues.attribute')
            ->where('is_active', true)
            ->orderBy('id')
            ->paginate(12);
    }

    public function show(Product $product): Product
    {
        abort_unless($product->is_active, 404);

        return $product->load('category.parent', 'brand', 'attributeValues.attribute');
    }
}
