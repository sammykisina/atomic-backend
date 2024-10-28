<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table): void {
            $table->boolean(column: 'train_at_origin')->after(column: 'originable_type');
            $table->boolean(column: 'train_at_destination')->after(column: 'destinationable_type');
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table): void {
            $table->dropColumn(columns: 'train_at_origin');
            $table->dropColumn(columns: 'train_at_destination');
        });
    }
};
