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
        Schema::create('order_payment_method', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2);
            $table->string('transaction_id', 100)->nullable()->unique(); // ID de transacción externa
            /* $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending'); */

            $table->foreignId('order_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_method_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payment_method');
    }
};
