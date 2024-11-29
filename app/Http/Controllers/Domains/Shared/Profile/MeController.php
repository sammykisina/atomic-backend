<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Profile;

use Domains\Shared\Models\User;
use Domains\Shared\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class MeController
{
    public function __invoke(Request $request): Response
    {
        $me  = QueryBuilder::for(subject: User::class)
            ->where('id', Auth::id())
            ->allowedIncludes(includes: ['role.permissions', 'department', 'region'])
            ->first();


        return response(
            content: [
                'message' => 'User profile fetched successful.',
                'me' => new UserResource(
                    resource: $me,
                ),
            ],
            status: Http::OK(),
        );
    }
}
