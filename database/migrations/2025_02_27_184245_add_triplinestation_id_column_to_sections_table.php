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
            $table->foreignId(column: 'triplinestation_id')
                ->nullable()
                ->after(column: 'station_id')
                ->references(column: 'id')
                ->on(table: 'stations')
                ->constrained();
        });
    }
};
