<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->boolean('favorite')->default(false);
            $table->string('street', length: 150);
            $table->unsignedInteger('street_number');
            $table->string('reference', length: 150);
            $table->morphs('addressable');

            $table->foreignId('district_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->softDeletes();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                CREATE UNIQUE INDEX addresses_single_favorite_per_owner
                ON addresses (addressable_type, addressable_id)
                WHERE favorite = true
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                DROP INDEX IF EXISTS addresses_single_favorite_per_owner
            ");
        }

        Schema::dropIfExists('addresses');
    }
};
