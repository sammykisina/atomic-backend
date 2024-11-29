<?php

declare(strict_types=1);

namespace Database\Factories;

use Domains\SuperAdmin\Models\County;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CountyFactory extends Factory
{
    /** @var class string<Model> */
    protected $model = County::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
