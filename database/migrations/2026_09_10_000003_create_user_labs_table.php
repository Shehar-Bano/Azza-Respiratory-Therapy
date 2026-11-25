<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('creatine', 8, 2)->nullable()->comment('Creatine / Creatinine (mg/dL or umol/L)');
            $table->decimal('sodium', 8, 2)->nullable()->comment('Sodium (Na)');
            $table->decimal('potassium', 8, 2)->nullable()->comment('Potassium (K+)');
            $table->decimal('hemoglobin', 8, 2)->nullable()->comment('Hemoglobin (g/dL)');
            $table->decimal('lactate', 8, 2)->nullable()->comment('Lactate (mmol/L)');
            $table->decimal('glucose', 8, 2)->nullable()->comment('Glucose (mg/dL)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_labs');
    }
};
