<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'journeys', callback: function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'train_id')
                ->references('id')
                ->on('trains')
                ->index()
                ->constrained()
                ->name('fk_journey_train');

            $table->boolean(column: 'is_active')->default(value: true);

            $table->string(column: 'direction');

            $table->boolean(column: 'is_authorized')->default(value: false);
            $table->json(column: 'shifts');

            $table->json(column: 'last_destination')->nullable();
            $table->json(column: 'requesting_location')->nullable();
            $table->json(column: 'logs')->nullable();

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journeys');
    }
};
