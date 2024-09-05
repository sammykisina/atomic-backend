<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('modules_users', function (Blueprint $table): void {
            $table->foreignId(column: 'user_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->string(column: 'module');

            $table->json(column: 'permissions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_users');
    }
};
