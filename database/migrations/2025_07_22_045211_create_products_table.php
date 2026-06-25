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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100)->unique();
            $table->string('model', length: 80);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);

            $table->foreignId('subcategory_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('brand_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
