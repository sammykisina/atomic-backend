<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Auth;

use Domains\Shared\Requests\ResetPasswordRequest;
use Domains\Shared\Services\AuthService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class RestPasswordController
{
    public function __construct(
        protected AuthService $authService,
    ) {}

    public function __invoke(ResetPasswordRequest $request): Response
    {
        $this->authService->resetPassword(
            user: $this->authService->getUserByEmail(
                email: $request->email,
            ),
            data: $request->validated(),
        );

        return response(
            content: [
                'message' => 'Password reset successfully.',
            ],
            status: Http::OK(),
        );
    }
}
