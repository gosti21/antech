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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 100)->nullable()->unique();
            $table->json('receiver_info');
            $table->enum('delivery_type', ['shipment', 'store_pickup']);
            $table->decimal('shipment_cost', 8, 2)->default(0);
            $table->enum('status', ['pending','preparing', 'ready_for_pickup', 'dispatched','in_transit','delivered', 'picked_up', 'failed', 'returned', 'cancelled'])->default('pending');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('order_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('address_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('shipping_company_id')->nullable()->constrained()
                ->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
