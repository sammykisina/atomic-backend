<?php

declare(strict_types=1);

namespace Database\Factories;

use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

final class InspectionScheduleFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = InspectionSchedule::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'inspector_id' => User::factory()->create([
                'type' => UserTypes::INSPECTOR,
                'is_admin' => false,
                'region' => 1,
            ]),
        ];
    }
}
