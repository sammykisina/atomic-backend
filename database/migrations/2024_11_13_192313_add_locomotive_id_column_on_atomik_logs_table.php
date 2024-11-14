<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(table: 'atomik_logs', callback: function (Blueprint $table): void {
            $table->foreignId(column: 'locomotive_number_id')
                ->after(column: 'train_id')
                ->index()
                ->nullable()
                ->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('atomik_logs', function (Blueprint $table): void {
            $table->dropColumn(columns: 'locomotive_number_id');
        });
    }
};
