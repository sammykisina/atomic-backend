<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('inspection_schedules', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'inspector_id')
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->constrained()
                ->name('fk_inspector');

            $table->time(column: 'time')->default(value: "08:00");
            $table->string(column: 'status')->default('ACTIVE');

            $table->foreignId(column: 'line_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'region_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->float(column: 'start_kilometer');
            $table->float(column: 'end_kilometer');

            $table->float(column: 'start_kilometer_latitude');
            $table->float(column: 'start_kilometer_longitude');

            $table->float(column: 'end_kilometer_latitude');
            $table->float(column: 'end_kilometer_longitude');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_schedules');
    }
};
