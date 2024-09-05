<?php

declare(strict_types=1);

use Domains\Shared\Models\User;
use Illuminate\Support\Str;
use PHPUnit\Framework\ExpectationFailedException;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\LazilyRefreshDatabase::class,
)->in('Feature');

// EXPECTATIONS
expect()->extend('toBePhoneNumber', function (): void {
    expect($this->value)->toBeString()->toStartWith('+');

    if (mb_strlen($this->value) < 10) {
        throw new ExpectationFailedException(
            message: "The phone number must be at least 10 digits long.",
        );
    }

    if( ! is_numeric(Str::of($this->value)->after('+')->remove([' ', '-'])->toString())) {
        throw new ExpectationFailedException(
            message: "Phone numbers must be numeric",
        );
    }
});

// LOGIN USER

function login(?User $user = null)
{
    return test()->actingAs($user ?? User::factory()->create());
}
