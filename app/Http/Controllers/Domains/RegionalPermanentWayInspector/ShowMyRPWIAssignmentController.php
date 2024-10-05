<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalPermanentWayInspector;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ShowMyRPWIAssignmentController
{
    public function __invoke(Request $request): Response
    {
        $my_rpwi_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', 'RPWI')
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'RPWI Assignments fetched successfully.',
                'my_rpwi_assignments' => UserRegionResource::collection(
                    resource: $my_rpwi_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
