<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domains\SuperAdmin\Models\County;
use Illuminate\Database\Seeder;

final class CountySeed extends Seeder
{
    /** @return void */
    public function run(): void
    {
        collect([
            "Mombasa", "Kwale", "Kilifi", "Tana River", "Lamu", "Taita-Taveta",
            "Garissa", "Wajir", "Mandera", "Marsabit", "Isiolo", "Meru", "Tharaka-Nithi",
            "Embu", "Kitui", "Machakos", "Makueni", "Nyandarua", "Nyeri", "Kirinyaga",
            "Murang’a", "Kiambu", "Turkana", "West Pokot", "Samburu", "Trans-Nzoia",
            "Uasin Gishu", "Elgeyo-Marakwet", "Nandi", "Baringo", "Laikipia", "Nakuru",
            "Narok", "Kajiado", "Kericho", "Bomet", "Kakamega", "Vihiga", "Bungoma",
            "Busia", "Siaya", "Kisumu", "Homa Bay", "Migori", "Kisii", "Nyamira",
            "Nairobi",
        ])->map(fn(?string $county) => County::factory()->create([
            'name' => $county,
        ]));
    }
}
