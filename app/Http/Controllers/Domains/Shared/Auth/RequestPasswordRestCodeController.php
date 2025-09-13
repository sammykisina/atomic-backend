<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Auth;

use Domains\Shared\Enums\OtpTypes;
use Domains\Shared\Requests\PasswordResetCodeRequest;
use Domains\Shared\Services\AuthService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class RequestPasswordRestCodeController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function __invoke(PasswordResetCodeRequest $request): Response
    {
        $user = $this->authService->getUserByEmail(
            email: $request->email,
        );

        $this->authService->opt(
            user: $user,
            type: OtpTypes::PASSWORD_RESET->value,
        );

        return response(
            content: [
                'message' => 'Your password reset code was send.Please check your email.',
            ],
            status: Http::OK(),
        );
    }

}
