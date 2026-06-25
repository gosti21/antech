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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', length: 255)->nullable();
            $table->enum('status', ['active', 'completed', 'abandoned', 'merged'])->default('active');
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
            // Índices
            $table->index(['user_id', 'status']);
            $table->index(['session_id', 'status']);
        });

        // ✅ índice parcial SOLO para PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                CREATE UNIQUE INDEX carts_user_active_unique
                ON carts (user_id)
                WHERE status = 'active'
            ");
            DB::statement("
                CREATE UNIQUE INDEX carts_session_active_unique
                ON carts (session_id)
                WHERE status = 'active'
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
                DROP INDEX IF EXISTS carts_user_active_unique
            ");
            DB::statement("
                DROP INDEX IF EXISTS carts_session_active_unique
            ");
        }

        Schema::dropIfExists('carts');
    }
};
