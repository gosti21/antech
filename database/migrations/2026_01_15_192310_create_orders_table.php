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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', length: 50)->unique();
            $table->enum('type_sale', ['online', 'store'])->default('online');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('igv', 10, 2);
            $table->decimal('shipment_cost', 8, 2)->default(0);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', ['pending', 'confirmed', 'processing', 'ready', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->json('checkout_snapshot')->nullable();

            // Para ventas POS
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();

            // Relaciones principales
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete()->cascadeOnUpdate();

            // Índices
            $table->index('order_number');
            $table->index(['type_sale', 'status']);
            $table->index(['customer_id', 'created_at']);
            $table->index(['branch_id', 'created_at']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
