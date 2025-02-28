<?php

declare(strict_types=1);

use App\Enums\Superadmin\SectionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(table: 'sections', callback: function (Blueprint $table): void {
            $table->string(column: 'section_type')->after(column: 'end_name')->default(value: SectionType::MAIN);
        });
    }
};
