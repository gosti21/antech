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
        Schema::create('document_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('number', length:20);
            $table->morphs('documentable');

            $table->foreignId('document_type_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['number', 'documentable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_numbers');
    }
};
