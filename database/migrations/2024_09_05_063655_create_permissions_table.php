<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'role_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->string(column: 'module');

            $table->json(column: 'abilities');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
