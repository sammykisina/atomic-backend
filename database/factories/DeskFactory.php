<?php

declare(strict_types=1);

namespace Database\Factories;

use Domains\Shared\Models\Desk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

final class DeskFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = Desk::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
