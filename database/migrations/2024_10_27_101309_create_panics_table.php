<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'panics', callback: function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'journey_id')
                ->index()
                ->constrained();

            $table->foreignId(column: 'shift_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text(column: 'issue');
            $table->text(column: 'description');

            $table->time(column: 'time');
            $table->date(column: 'date');

            $table->float(column: 'latitude');
            $table->float(column: 'longitude');

            $table->boolean(column: 'is_acknowledge')->default(value: false);
            $table->time(column: 'time_of_acknowledge')->nullable();
            $table->date(column: 'date_of_acknowledge')->nullable();


            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'panics');
    }
};
