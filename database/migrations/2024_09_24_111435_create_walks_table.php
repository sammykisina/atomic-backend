<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'walks', callback: function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'inspection_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->float(column: 'latitude');
            $table->float(column: 'longitude');
            $table->time(column: 'time');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'walks');
    }
};
