<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product_attribute', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();

            $table->primary(['category_id', 'product_attribute_id']);
            // MySQL limits index names to 64 characters, so use a short name.
            $table->index(['product_attribute_id', 'category_id'], 'category_attribute_reverse_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product_attribute');
    }
};
