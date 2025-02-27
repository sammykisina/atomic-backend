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
        Schema::table(table: 'stations', callback: function (Blueprint $table): void {
            $table->boolean(column: 'has_trip_line')
                ->nullable()
                ->after(column: 'is_yard')
                ->default(value: false);
        });
    }
};
