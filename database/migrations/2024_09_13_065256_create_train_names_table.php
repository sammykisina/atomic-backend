<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'train_names', callback: function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'name')->unique();
            $table->boolean(column: 'is_active')->default(value: false);

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'train_names');
    }
};
