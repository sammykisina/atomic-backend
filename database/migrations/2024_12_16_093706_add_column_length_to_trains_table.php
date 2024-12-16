<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(table: 'trains', callback: function (Blueprint $table): void {
            $table->integer(column: 'length')->nullable()->after(column: 'number_of_wagons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(table: 'trains', callback: function (Blueprint $table): void {
            $table->dropColumn(columns: 'length');
        });
    }
};
