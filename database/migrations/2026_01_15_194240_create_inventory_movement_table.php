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
        Schema::create('inventory_movement', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('quantity');

            $table->foreignId('branch_variant_id')
      ->constrained(table: 'branch_variant', indexName: 'fk_inventory_movement_branch_variant')
      ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('movement_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movement');
    }
};
