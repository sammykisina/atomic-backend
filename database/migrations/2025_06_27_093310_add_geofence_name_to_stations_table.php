<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(table: 'stations', callback: function (Blueprint $table): void {
            $table->string(column: 'geofence_name')->nullable()->after(column: 'position_from_line');
        });
    }
};
