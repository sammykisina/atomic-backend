<?php

declare(strict_types=1);

namespace Database\Factories;

use Domains\Shared\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DepartmentFactory extends Factory
{
    /** @var class-string<Department> */
    protected $model = Department::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
        ];
    }
}
