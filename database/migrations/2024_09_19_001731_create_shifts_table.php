<?php

declare(strict_types=1);

use Domains\Operator\Enums\ShiftStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'desk_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->date(column: 'day');
            $table->time(column: 'from');
            $table->time(column: 'to');

            $table->string(column: 'status')->default(value: ShiftStatuses::PENDING); // PENDING, CONFIRMED,
            $table->boolean(column: 'active')->default(value: false);

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(
                ['desk_id', 'user_id', 'day', 'from', 'to'],
                'unique_shift_per_desk_user',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
