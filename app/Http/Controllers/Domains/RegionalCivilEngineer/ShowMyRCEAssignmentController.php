<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalCivilEngineer;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ShowMyRCEAssignmentController
{
    public function __invoke(Request $request): Response
    {
        $my_rce_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', 'RCE')
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'RCE Assignments fetched successfully.',
                'my_rce_assignments' => UserRegionResource::collection(
                    resource: $my_rce_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
