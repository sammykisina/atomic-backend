<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(table: 'speeds', callback: function (Blueprint $table): void {
            $table->id();

            $table->foreignId(column: 'line_id')
                ->index()
                ->constrained();

            $table->unsignedBigInteger(column: 'areable_id');
            $table->string(column: 'areable_type');

            $table->float(column: 'start_kilometer');
            $table->float(column: 'end_kilometer');

            $table->integer(column: 'speed');

            $table->float(column: 'start_kilometer_latitude');
            $table->float(column: 'start_kilometer_longitude');

            $table->float(column: 'end_kilometer_latitude');
            $table->float(column: 'end_kilometer_longitude');


            $table->timestamp(column: 'created_at')->useCurrent();
            $table->timestamp(column: 'updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(table: 'speeds');
    }
};
