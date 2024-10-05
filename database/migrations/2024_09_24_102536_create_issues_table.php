<?php

declare(strict_types=1);

use Domains\Inspector\Enums\IssueStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'issues', callback: function (Blueprint $table): void {
            $table->id();

            $table->float(column: 'latitude');
            $table->float(column: 'longitude');

            $table->float(column: 'issue_kilometer');

            $table->foreignId(column: 'inspection_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'issue_name_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string(column: 'condition');
            $table->string(column: 'description')->nullable();
            $table->string(column: 'image_url')->nullable();

            $table->string(column : 'status')->default(IssueStatuses::PENDING);

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'issues');
    }
};
