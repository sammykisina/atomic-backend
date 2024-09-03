<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table): void {
            $table->id();

            $table->string('name')->unique();


            $table->foreignId('owner_id')
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->name('fk_income_owner');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
