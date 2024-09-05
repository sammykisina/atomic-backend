<?php

declare(strict_types=1);

namespace Database\Factories;

use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserFactory extends Factory
{
    /** @var class-string<Model> */
    protected $model = User::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make(
                value: 'password',
            ),
            'remember_token' => Str::random(
                length: 10,
            ),
            'email_verified_at' => now(),
        ];
    }

    public function unverified(): UserFactory
    {
        return $this->state(
            state: fn(array $attributes) => [
                'email_verified_at' => null,
            ],
        );
    }
}
