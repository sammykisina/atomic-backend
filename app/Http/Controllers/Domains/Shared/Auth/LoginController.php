<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Auth;

use Domains\Shared\Requests\LoginRequest;
use Domains\Shared\Resources\UserResource;
use Domains\Shared\Services\AuthService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class LoginController
{
    public function __construct(
        protected AuthService $authService,
    ) {}

    /**
     * @param LoginRequest $request
     * @return void
     */
    public function __invoke(LoginRequest $request): Response
    {
        $user = $this->authService->login(
            data: $request->validated(),
        );

        if ($user) {
            $abilities = $user->is_admin ? ['*'] : [];

            $token = $user->createToken(
                name: 'auth',
                abilities: $abilities,
                expiresAt: now()->addDay(),
            )->plainTextToken;

            return response(
                content: [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'message' => 'Login Successful',
                ],
                status: Http::OK(),
            );
        }

        return response(
            content: [
                'message' => 'Login Failed.Check your credentials',
            ],
            status: Http::UNPROCESSABLE_ENTITY(),
        );
    }
}
