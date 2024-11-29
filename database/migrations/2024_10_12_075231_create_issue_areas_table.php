<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('issue_areas', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'issue_id')
                ->index()
                ->constrained();

            $table->foreignId(column: 'line_id')
                ->index()
                ->nullOnDelete();

            $table->foreignId(column: 'station_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'section_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string(column: 'speed_suggestion')->nullable();
            $table->string(column: 'speed_suggestion_comment')->nullable();

            $table->string(column: 'speed_suggestion_status')->nullable();

            $table->string(column: 'proposed_speed')->nullable();
            $table->string(column: 'proposed_speed_comment')->nullable();


            $table->string(column: 'reverted_speed')->nullable();
            $table->string(column: 'reverted_speed_comment')->nullable();

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_areas');
    }
};
