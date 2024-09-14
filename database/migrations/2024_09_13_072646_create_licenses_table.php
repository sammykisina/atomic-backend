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

            $table->foreignId(column: 'journey_id')
                ->index()
                ->constrained();

            $table->foreignId(column: 'line_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'section_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'station_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(column: 'main_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'stations')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_main_line_section_in_station');

            $table->foreignId(column: 'owner_id')
                ->nullable()
                ->references(column: 'id')
                ->on(table: 'users')
                ->index()
                ->constrained()
                ->nullOnDelete()
                ->name('fk_license_owner');

            $table->foreignId(column: 'loop_id')
                ->index()
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string(column: 'status')->default(value: LicenseStatuses::PENDING);

            $table->string(column: 'direction');


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
