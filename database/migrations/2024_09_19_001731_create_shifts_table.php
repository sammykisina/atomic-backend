<?php

declare(strict_types=1);

use Domains\Operator\Enums\ShiftStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'desk_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('line_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId(column: 'shift_start_station_id')
                ->references(column: 'id')
                ->on('stations')
                ->index()
                ->constrained()
                ->name('fk_shift_start_station');

            $table->foreignId(column: 'shift_end_station_id')
                ->references('id')
                ->on('stations')
                ->index()
                ->constrained()
                ->name('fk_shift_end_station');

            $table->date(column: 'day'); // day of the shift
            $table->time(column: 'from'); // when the shift will start
            $table->time(column: 'to'); // when the shift will end
            $table->json(column: 'stations');

            $table->string(column: 'status')->default(value: ShiftStatuses::PENDING); // PENDING, CONFIRMED,
            $table->boolean(column: 'active')->default(value: false);

            $table->foreignId(column: 'creator_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_shift_creator');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(
                ['desk_id', 'user_id', 'shift_start_station_id', 'shift_end_station_id', 'day', 'from', 'to'],
                'unique_shift_per_desk_user_stations',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
