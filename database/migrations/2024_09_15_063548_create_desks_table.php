<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('desks', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'name');
            $table->foreignId(column: 'group_id')
                ->index()
                ->constrained();

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(columns: ['name', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desks');
    }
};
