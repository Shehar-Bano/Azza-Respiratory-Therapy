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
        Schema::create('user_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('sbp', 8, 2)->nullable()->comment('Systolic Blood Pressure (mmHg)');
            $table->decimal('dbp', 8, 2)->nullable()->comment('Diastolic Blood Pressure (mmHg)');
            $table->decimal('hr', 8, 2)->nullable()->comment('Heart Rate (bpm)');
            $table->decimal('rr', 8, 2)->nullable()->comment('Respiratory Rate (mmHg / breaths per min)');
            $table->decimal('spo2', 8, 2)->nullable()->comment('Oxygen Saturation (%)');
            $table->decimal('temp', 8, 2)->nullable()->comment('Temperature (°C)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vitals');
    }
};
