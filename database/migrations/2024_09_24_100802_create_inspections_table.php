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

            $table->foreignId(column: 'inspector_id')
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->constrained()
                ->name('fk_inspector');

            $table->timestamp(column: 'time');
            $table->boolean(column: 'active');
            $table->string(column: 'status');

            $table->foreignId(column: 'line_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->float(column: 'start_kilometer');
            $table->float(column: 'end_kilometer');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'inspections');
    }
};
