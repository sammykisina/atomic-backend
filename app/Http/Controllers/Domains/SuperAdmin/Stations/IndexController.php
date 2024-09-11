<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Stations;

use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $stations  = QueryBuilder::for(Station::class)
            ->allowedIncludes('line', 'section')
            ->get();

        return response(
            content: [
                'message' => 'Stations fetched successfully.',
                'stations' => StationResource::collection(
                    resource: $stations,
                ),
            ],
            status: Http::OK(),
        );
    }
}
