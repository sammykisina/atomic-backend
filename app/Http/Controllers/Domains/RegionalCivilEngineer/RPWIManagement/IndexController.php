<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalCivilEngineer\RPWIManagement;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $rpwi_assignments  = QueryBuilder::for(subject: UserRegion::class)
            ->where('type', 'RPWI')
            ->allowedIncludes('user', 'line', 'startStation', 'endStation', 'region')
            ->where('owner_id', Auth::id())
            ->get();

        return response(
            content: [
                'message' => 'RPWI Assignments fetched successfully.',
                'rpwi_assignments' => UserRegionResource::collection(
                    resource: $rpwi_assignments,
                ),
            ],
            status: Http::OK(),
        );
    }
}
