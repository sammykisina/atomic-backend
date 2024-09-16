<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domains\Shared\Models\Desk;
use Illuminate\Database\Seeder;

final class DeskSeeder extends Seeder
{
    /** @return void */
    public function run(): void
    {
        Desk::factory()->create([
            'name' => 'Desk 1',
        ]);

        Desk::factory()->create([
            'name' => 'Desk 2',
        ]);
    }
}
