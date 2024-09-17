<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('desks_users', function (Blueprint $table): void {
            $table->foreignId(column: 'desk_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->index()
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean(column: 'status')->default(value: false);

            $table->timestamp(column: 'created_at')->useCurrent();

            $table->unique(['desk_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desks_users');
    }
};
