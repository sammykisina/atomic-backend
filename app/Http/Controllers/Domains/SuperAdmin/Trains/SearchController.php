<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Trains;

use Domains\SuperAdmin\Models\Train;
use Domains\SuperAdmin\Resources\TrainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class SearchController
{
    public function __invoke(Request $request): Response
    {
        $train  = QueryBuilder::for(subject: Train::class)
            ->allowedFilters([AllowedFilter::exact('name')])
            ->allowedIncludes(includes: [
                'line',
                'origin',
                'destination',
                'driver',
                'locomotiveNumber',
            ])
            ->first();

        return response(
            content: [
                'message' => 'Train fetched successfully.',
                'train' => $train ? new TrainResource(
                    resource: $train,
                ) : null,
            ],
            status: Http::OK(),
        );
    }
}
