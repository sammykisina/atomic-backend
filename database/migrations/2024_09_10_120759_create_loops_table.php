<?php

declare(strict_types=1);

use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('loops', function (Blueprint $table): void {
            $table->id();

            $table->float(column: 'distance');
            $table->string(column: 'position');

            $table->foreignId(column: 'line_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId(column: 'station_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->float(column: 'start_latitude_top');
            $table->float(column: 'start_longitude_top');

            $table->float(column: 'start_latitude_bottom');
            $table->float(column: 'start_longitude_bottom');

            $table->float(column: 'end_latitude_top');
            $table->float(column: 'end_longitude_top');

            $table->float(column: 'end_latitude_bottom');
            $table->float(column: 'end_longitude_bottom');

            $table->string(column: 'status')->default(
                value: StationSectionLoopStatuses::GOOD,
            );


            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['line_id', 'station_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loops');
    }
};
