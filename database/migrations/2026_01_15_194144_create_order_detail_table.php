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
        Schema::create('order_detail', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 255);
            $table->string('variant_sku', 100);
            $table->decimal('unit_price', total: 10, places: 2);
            $table->decimal('discount', 8, 2)->default(0);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', total:11, places:2);

            $table->foreignId('order_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

           $table->foreignId('branch_variant_id')
      ->constrained(table: 'branch_variant', indexName: 'fk_order_detail_branch_variant') 
      ->cascadeOnDelete()->cascadeOnUpdate();


            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_detail');
    }
};
