<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(table: 'locomotive_numbers', callback: function (Blueprint $table): void {
            $table->foreignId(column: 'driver_id')
                ->after(column: 'number')
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->nullable()
                ->constrained();
        });
    }

    public function down(): void
    {
        Schema::table(table: 'locomotive_numbers', callback: function (Blueprint $table): void {
            $table->dropColumn(columns: 'driver_id');
        });
    }
};
