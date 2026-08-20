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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id')->unique();
            $table->string('title');
            $table->string('price')->nullable();
            $table->string('price_usd')->nullable();
            $table->string('price_sar')->nullable();
            $table->string('currency')->default('USD');
            $table->string('currency_usd')->default('USD');
            $table->string('currency_sar')->default('SAR');
            $table->integer('duration_days')->default(30);
            $table->string('access')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
