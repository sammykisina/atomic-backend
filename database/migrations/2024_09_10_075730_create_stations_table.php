<?php

declare(strict_types=1);

use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'name');
            $table->float(column: 'start_kilometer');
            $table->float(column: 'end_kilometer');

            $table->float(column: 'start_latitude_top');
            $table->float(column: 'start_longitude_top');

            $table->float(column: 'start_latitude_bottom');
            $table->float(column: 'start_longitude_bottom');

            $table->float(column: 'end_latitude_top');
            $table->float(column: 'end_longitude_top');

            $table->float(column: 'end_latitude_bottom');
            $table->float(column: 'end_longitude_bottom');

            $table->boolean(column: 'is_yard')->default(
                value: false,
            );

            $table->integer(column: 'speed')->default(value: 40);

            $table->string(column: 'status')->default(
                value: StationSectionLoopStatuses::GOOD,
            );

            $table->foreignId(column: 'line_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->string(column: 'position_from_line');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
