<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\PermanentWayInspector;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ShowMyPWIAssignmentController
{
    public function __invoke(Request $request): Response
    {
        $my_pwi_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', 'PWI')
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->get();

        return response(
            content: [
                'message' => 'PWI Assignments fetched successfully.',
                'my_pwi_assignments' => UserRegionResource::collection(
                    resource: $my_pwi_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
