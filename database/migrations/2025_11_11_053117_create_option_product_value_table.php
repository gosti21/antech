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
        Schema::create('option_product_value', function (Blueprint $table) {
            $table->id();

            $table->foreignId('option_product_id')->constrained(table: 'option_product', indexName: 'option_product_id')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('option_value_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['option_product_id', 'option_value_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_product_value');
    }
};
