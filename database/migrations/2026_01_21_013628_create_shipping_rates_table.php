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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('delivery_price', 10, 2);
            // Tiempos de entrega (solo para envío a domicilio)
            $table->unsignedTinyInteger('min_delivery_days')->default(2); // Mínimo 2 días
            $table->unsignedTinyInteger('max_delivery_days')->default(5);
            
            $table->morphs('shippable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
