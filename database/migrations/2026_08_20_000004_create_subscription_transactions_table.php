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
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('plan_id');
            $table->string('cart_id')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('amount')->nullable();
            $table->string('currency')->default('SAR');
            $table->string('payment_gateway')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_first_six')->nullable();
            $table->string('card_last_four')->nullable();
            $table->string('payment_status')->default('success');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->json('gateway_response')->nullable();
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_transactions');
    }
};
