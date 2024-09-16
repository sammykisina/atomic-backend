<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'journey_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->float(column: "latitude");
            $table->float(column: "longitude");

            $table->foreignId(column: 'station_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'main_id')
                ->nullable()
                ->references('id')
                ->on('stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_main_line_section_in_station');

            $table->foreignId(column: 'loop_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'section_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->boolean(column: 'status');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
