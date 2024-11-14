<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'atomik_logs', callback: function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'type');


            $table->foreignId(column: 'train_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'locomotive_number_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string(column: 'current_location');

            $table->unsignedBigInteger(column: 'resourceble_id');
            $table->string(column: 'resourceble_type');

            $table->foreignId(column: 'actor_id')
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_atomik_log_actor');

            $table->foreignId(column: 'receiver_id')
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_atomik_log_receiver');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'atomik_logs');
    }
};
