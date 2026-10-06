<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value', 100);
            $table->string('label');
            $table->decimal('numeric_value', 14, 4)->nullable();
            $table->timestamps();

            $table->unique(['product_attribute_id', 'value']);
            $table->index(['product_attribute_id', 'numeric_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
