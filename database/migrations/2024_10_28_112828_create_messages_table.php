<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'messages', callback: function (Blueprint $table): void {
            $table->id();


            $table->text(column: 'message');

            $table->foreignId(column: 'sender_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_message_sender');

            $table->foreignId(column: 'receiver_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_message_receiver');

            $table->timestamp(column: 'read_at')->nullable();

            $table->foreignId(column: 'locomotive_number_id')
                ->index()
                ->nullable()
                ->constrained();

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'messages');
    }
};
