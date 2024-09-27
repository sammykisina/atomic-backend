<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'inspections', callback: function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'inspection_schedule_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date(column: 'date');
            $table->time(column: 'time');
            $table->boolean(column: 'is_active');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'inspections');
    }
};
