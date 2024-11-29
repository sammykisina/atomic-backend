<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'type');
            $table->string(column: 'code');
            $table->boolean(column: 'active');

            $table->foreignId(column: 'user_id')->constrained(
                table: 'users',
            );

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
