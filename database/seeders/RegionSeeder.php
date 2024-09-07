<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domains\Shared\Models\Region;
use Illuminate\Database\Seeder;

final class RegionSeeder extends Seeder
{
    /** @return void */
    public function run(): void
    {
        Region::factory()->create([
            'name' => 'Eastern Region',
        ]);

        Region::factory()->create([
            'name' => 'Central Region',
        ]);

        Region::factory()->create([
            'name' => 'Western 1 Region',
        ]);
        Region::factory()->create([
            'name' => 'Western 2 Region',
        ]);
    }
}
