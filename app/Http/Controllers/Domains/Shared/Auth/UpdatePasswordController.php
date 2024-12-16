<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Auth;

use Domains\Shared\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JustSteveKing\StatusCode\Http;

final class UpdatePasswordController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdatePasswordRequest $request)
    {
        if ( !  Auth::user()->update(attributes: [
            'password' => Hash::make(value: $request->validated(key: 'password')),
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Password update failed. Please try again later',
            );
        }


        return response(
            content: [
                'message' => 'Password updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
