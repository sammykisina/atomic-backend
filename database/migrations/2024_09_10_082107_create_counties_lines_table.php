<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('counties_lines', function (Blueprint $table): void {
            $table->foreignId(column: 'line_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();


            $table->foreignId(column: 'county_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->unique(['line_id', 'county_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counties_lines');
    }
};
