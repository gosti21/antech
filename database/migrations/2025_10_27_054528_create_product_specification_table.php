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
        Schema::create('product_specification', function (Blueprint $table) {
            $table->id();
            $table->text('value');

            $table->foreignId('product_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('specification_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['product_id', 'specification_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specification');
    }
};
