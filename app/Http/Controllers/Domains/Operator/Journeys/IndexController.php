<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

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
        $journeys = QueryBuilder::for(subject: Journey::class)
            ->with(relations: [
                'train.line',
                'train.origin',
                'train.destination',
                'train.locomotiveNumber',
                'train.driver',
                'licenses'
            ])
            ->allowedFilters([AllowedFilter::exact('is_active')])
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
