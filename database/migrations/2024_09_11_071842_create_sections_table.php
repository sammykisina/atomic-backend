<?php

declare(strict_types=1);

use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'start_name');
            $table->string(column: 'end_name');

            $table->float(column: 'start_kilometer');
            $table->float(column: 'end_kilometer');

            $table->float(column: 'start_latitude');
            $table->float(column: 'start_longitude');

            $table->float(column: 'end_latitude');
            $table->float(column: 'end_longitude');

            $table->integer(column: 'speed')->default(value: 40);

            $table->float(column: 'number_of_kilometers_to_divide_section_to_subsection');

            $table->foreignId(column: 'line_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId(column: 'station_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->string(column: 'status')->default(
                value: StationSectionLoopStatuses::GOOD,
            );



            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['line_id', 'station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
