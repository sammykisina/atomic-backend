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
        Schema::table(table: 'sections', callback: function (Blueprint $table): void {
            $table->string(column: 'has_trip_line')
                ->nullable()
                ->after(column: 'status')
                ->default(value: false);
        });
    }
};
