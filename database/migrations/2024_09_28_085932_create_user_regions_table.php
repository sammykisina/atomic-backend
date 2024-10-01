<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_regions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'user_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'region_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();



            $table->foreignId(column: 'line_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string(column: 'type');


            $table->foreignId(column: 'start_station_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_start_station');

            $table->foreignId(column: 'end_station_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_end_station');

            $table->float(column: 'start_kilometer')->nullable();
            $table->float(column: 'end_kilometer')->nullable();

            $table->boolean(column: 'is_active')->default(value: true);

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_regions');
    }
};
