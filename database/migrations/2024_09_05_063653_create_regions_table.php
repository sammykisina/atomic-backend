<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'name')->unique();

            $table->foreignId(column: 'creator_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_region_creator');

            $table->foreignId(column: 'updater_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_region_updater');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
