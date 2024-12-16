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
        Schema::table(table: 'messages', callback: function (Blueprint $table): void {
            $table->boolean(column: 'is_active')->default(value: true)->after(column: 'message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(table: 'messages', callback: function (Blueprint $table): void {
            $table->dropColumn(columns: 'is_active');
        });
    }
};
