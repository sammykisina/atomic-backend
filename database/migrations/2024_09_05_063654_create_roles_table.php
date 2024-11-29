<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();

            $table->string('name')->unique();
            $table->string('description');

            $table->foreignId(column: 'creator_id')
                ->nullable()
                ->references('id')
                ->on('roles')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_role_creator');

            $table->foreignId(column: 'updater_id')
                ->nullable()
                ->references('id')
                ->on('roles')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_role_updater');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
