<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('paths', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'license_id')
                ->index()
                ->constrained();

            $table->foreignId(column: 'origin_station_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_origin_station');

            $table->foreignId(column: 'origin_main_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_origin_main_line');

            $table->foreignId(column: 'origin_loop_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'loops')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_origin_loop');

            $table->foreignId(column: 'section_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'destination_station_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_destination_station');



            $table->foreignId(column: 'destination_main_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_destination_main_line');

            $table->foreignId(column: 'destination_loop_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'loops')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_destination_loop');


            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paths');
    }
};
