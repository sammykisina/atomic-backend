<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'groups', callback: function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'name')->unique();
            $table->string(column: 'description');
            $table->json(column: 'stations');
            $table->json(column: 'lines');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'groups');
    }
};
