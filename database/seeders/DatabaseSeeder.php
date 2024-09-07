<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /** @return void */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RegionSeeder::class,
        ]);
    }
}
