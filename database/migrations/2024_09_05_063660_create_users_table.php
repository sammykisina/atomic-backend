<?php

declare(strict_types=1);

use Domains\Shared\Enums\ModelStatuses;
use Domains\Shared\Enums\WorkStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();

            $table->string(column: 'first_name');
            $table->string(column: 'last_name');
            $table->string(column: 'email')->unique();
            $table->string(column: 'phone')->unique();
            $table->string(column:'employee_id')->unique();
            $table->string(column:'national_id')->unique();
            $table->string(column:'type');
            $table->boolean(column:'is_admin')->default(value: false);

            $table->foreignId(column: 'department_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'region_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'role_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamp(column: 'email_verified_at')->nullable();
            $table->string(column: 'image_url')->nullable();
            $table->string(column: 'password');

            $table->string(column: 'status')->default(value: ModelStatuses::ACTIVE);
            $table->string(column: 'work_status')->default(value: WorkStatuses::ON_THE_JOB);

            $table->rememberToken();

            $table->foreignId(column: 'creator_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_user_creator');

            $table->foreignId(column: 'updater_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_user_updater');

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
