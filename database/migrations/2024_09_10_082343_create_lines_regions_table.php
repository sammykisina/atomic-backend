<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('lines_regions', function (Blueprint $table): void {
            $table->foreignId(column: 'line_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();


            $table->foreignId(column: 'region_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->unique(['line_id', 'region_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lines_regions');
    }
};
