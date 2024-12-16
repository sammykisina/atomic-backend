<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(table: 'licenses', callback: function (Blueprint $table): void {
            $table->string(column: 'line_to_use')->nullable()->after(column: 'distance_to_stop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(table: 'licenses', callback: function (Blueprint $table): void {
            $table->dropColumn(columns: 'line_to_use');
        });
    }
};
