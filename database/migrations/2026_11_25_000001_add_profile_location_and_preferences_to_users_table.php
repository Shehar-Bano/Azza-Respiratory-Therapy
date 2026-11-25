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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location', 500)->nullable()->after('fcm_token');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 150)->nullable()->after('location');
            }
            if (!Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('users', 'push_enabled')) {
                $table->boolean('push_enabled')->default(true)->after('profile_image');
            }
            if (!Schema::hasColumn('users', 'notification_channels')) {
                $table->json('notification_channels')->nullable()->after('push_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter([
                'phone',
                'location',
                'city',
                'latitude',
                'longitude',
                'profile_image',
                'push_enabled',
                'notification_channels',
            ], fn ($col) => Schema::hasColumn('users', $col));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
