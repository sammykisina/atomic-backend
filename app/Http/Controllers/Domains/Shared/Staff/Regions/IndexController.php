<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Staff\Regions;

use Domains\Shared\Models\Region;
use Domains\Shared\Resources\RegionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $regions = QueryBuilder::for(Region::class)
            ->allowedFilters([])
            ->get();

        return response(
            content: [
                'message' => 'Regions fetched successfully.',
                'regions' => RegionResource::collection(
                    resource: $regions,
                ),
            ],
            status: Http::OK(),
        );
    }
}
