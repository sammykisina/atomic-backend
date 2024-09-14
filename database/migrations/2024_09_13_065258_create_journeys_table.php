<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('journeys', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'driver_id')
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->name('fk_journey_creator');

            $table->string(column: 'train');
            $table->string(column: 'service_order')->unique();

            $table->integer(column: "number_of_coaches");

            $table->foreignId(column: 'origin_station_id')
                ->references('id')
                ->on('stations')
                ->index()
                ->constrained()
                ->name('fk_origin_station');

            $table->foreignId(column: 'destination_station_id')
                ->references('id')
                ->on('stations')
                ->index()
                ->constrained()
                ->name('fk_destination_station');

            $table->boolean(column: 'status')->default(value: true);

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journeys');
    }
};
