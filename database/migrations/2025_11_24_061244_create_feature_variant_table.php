<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_variant', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('option_product_value_id')->constrained(table: 'option_product_value', indexName: 'option_product_value_id')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['variant_id', 'option_product_value_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_variant');
    }
};
