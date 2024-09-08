<?php

declare(strict_types=1);

namespace Database\Factories;

use Domains\Shared\Enums\ModelStatuses;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
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
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->e164PhoneNumber(),
            'employee_id' => $this->faker->phoneNumber(),
            'national_id' => '34996980',
            'type'  => $this->faker->randomElement(UserTypes::class),
            'status' => $this->faker->randomElement(ModelStatuses::class),
            'work_status' => $this->faker->randomElement(WorkStatuses::class),
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
