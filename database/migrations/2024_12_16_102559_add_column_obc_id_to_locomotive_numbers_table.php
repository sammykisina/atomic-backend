<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('locomotive_numbers', function (Blueprint $table): void {
            $table->foreignId(column: 'obc_id')
                ->after(column: 'number')
                ->index()
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('locomotive_numbers', function (Blueprint $table): void {
            $table->dropColumn('obc_id');
        });
    }
};
