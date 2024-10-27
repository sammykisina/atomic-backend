<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(table: 'journeys', callback: function (Blueprint $table): void {
            $table->boolean(column: 'is_authorized')->default(value: false)->after(column: 'train_id');
            $table->json(column: 'shifts')->after(column: 'is_authorized');
        });
    }

    public function down(): void
    {
        Schema::table(table: 'journeys', callback: function (Blueprint $table): void {
            $table->dropColumn(columns: 'is_authorized');
        });
    }
};
