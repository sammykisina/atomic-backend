<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'issue_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->json(column: 'gang_men');

            $table->foreignId(column: 'resolver_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_issue_resolver');

            $table->string('image_url')->nullable();
            $table->string('comment')->nullable();


            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
