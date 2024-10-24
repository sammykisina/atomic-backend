<?php

declare(strict_types=1);

use Domains\Driver\Enums\LicenseStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'licenses', callback: function (Blueprint $table): void {
            $table->id();

            $table->integer(column: 'license_number')->unique();

            $table->foreignId(column: 'journey_id')
                ->index()
                ->constrained();

            $table->string(column: 'status')->default(value: LicenseStatuses::PENDING);

            $table->string(column: 'direction');

            $table->unsignedBigInteger('originable_id');
            $table->string('originable_type');

            $table->json(column: 'through');

            $table->unsignedBigInteger('destinationable_id');
            $table->string('destinationable_type');

            $table->foreignId(column: 'issuer_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_issuer');

            $table->foreignId(column: 'rejector_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_rejector');

            $table->timestamp(column: 'issued_at')->nullable();
            $table->timestamp(column: 'rejected_at')->nullable();
            $table->timestamp(column: 'confirmed_at')->nullable();

            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
