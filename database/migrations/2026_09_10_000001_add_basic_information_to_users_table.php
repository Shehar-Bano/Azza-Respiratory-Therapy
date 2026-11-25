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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('fcm_token');
            $table->string('gender', 20)->nullable()->after('age'); // or sex (e.g. M, F)
            $table->decimal('height', 8, 2)->nullable()->after('gender'); // in cm
            $table->decimal('weight', 8, 2)->nullable()->after('height'); // in kg
            $table->decimal('bmi', 8, 2)->nullable()->after('weight'); // kg/m^2
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age', 'gender', 'height', 'weight', 'bmi']);
        });
    }
};
