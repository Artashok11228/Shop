<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_value_product', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();

            $table->primary(['product_id', 'attribute_value_id']);
            // The reverse order helps filters find products for a selected value.
            $table->index(['attribute_value_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product');
    }
};
