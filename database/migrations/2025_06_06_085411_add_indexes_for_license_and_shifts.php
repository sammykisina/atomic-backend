<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) shifts: index on (user_id, status, active)
        Schema::table('shifts', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'active'], 'idx_shifts_user_status_active');
        });

        // 2) licenses: index on (journey_id, status, created_at)
        Schema::table('licenses', function (Blueprint $table) {
            $table->index(['journey_id', 'status', 'created_at'], 'idx_licenses_journey_status_created');
            // (Optional) also index journey_id alone if you frequently query it
            $table->index('journey_id', 'idx_licenses_journey');
        });

        // 3) journeys → train_id
        Schema::table('journeys', function (Blueprint $table) {
            $table->index('train_id', 'idx_journeys_train_id');
        });

        // 4) trains → driver_id
        Schema::table('trains', function (Blueprint $table) {
            $table->index('driver_id', 'idx_trains_driver_id');
        });
    }
};
