<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\JourneyManagement;

use Domains\Driver\Models\Journey;
use Domains\Driver\Resources\JourneyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $journeys  = QueryBuilder::for(subject: Journey::class)
            ->allowedFilters(filters: [
                AllowedFilter::exact(name: 'status', ),
            ])
            ->allowedIncludes('origin', 'destination', 'driver')
            ->get();

        return response(
            content: [
                'message' => 'Journeys fetched successfully.',
                'journeys' => JourneyResource::collection(
                    resource: $journeys,
                ),
            ],
            status: Http::OK(),
        );
    }
}
