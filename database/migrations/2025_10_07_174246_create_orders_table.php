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

            // Foreign keys
            $table->foreignId('user_id')->constrained()->nullOnDelete();
            $table->foreignId('delivery_agent_id')->nullable()->constrained('users')->nullOnDelete();

            // Order Details
            $table->string('order_number')->unique();
            $table->enum('status', [
                'pending', 'confirmed', 'dispatched', 'delivered', 'cancelled', 'refunded',
            ])->default('pending');

            $table->decimal('total_amount', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);

            // Payment Details
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cod', 'credit_card', 'debit_card', 'wallet', 'upi'])->default('cod');
            $table->string('transaction_id')->nullable();

            // Address Details
            $table->string('delivery_address');
            $table->string('delivery_city');
            $table->string('delivery_state');
            $table->string('delivery_zip');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Estimated and actual delivery
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // JSON data for AI or analytics
            $table->json('meta')->nullable();

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
