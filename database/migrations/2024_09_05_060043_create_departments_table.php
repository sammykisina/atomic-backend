<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'name')->unique();
            $table->string(column: 'description')->nullable();

            $table->foreignId(column: 'creator_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_department_creator');

            $table->foreignId(column: 'updater_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_department_updater');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
