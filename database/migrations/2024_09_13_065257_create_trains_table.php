<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'trains', callback: function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'train_name_id')
                ->index()
                ->constrained();

            $table->foreignId(column: 'driver_id')
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->name('fk_train_driver');

            $table->string(column: 'service_order')->unique();

            $table->foreignId(column: 'locomotive_number_id')
                ->references('id')
                ->on('locomotive_numbers')
                ->index()
                ->constrained()
                ->name('fk_locomotive_numbers');

            $table->string(column: 'tail_number');

            $table->integer(column: "number_of_wagons");
            $table->date(column: "date");
            $table->time(column: "time");
            $table->integer(column: "tonnages");

            $table->foreignId(column: 'line_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

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



            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};
