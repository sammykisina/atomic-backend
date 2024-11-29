<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Trains;

use Domains\SuperAdmin\Models\Train;
use Domains\SuperAdmin\Resources\TrainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $trains  = QueryBuilder::for(subject: Train::class)
            ->allowedIncludes(includes: [
                'trainName',
                'line',
                'origin',
                'destination',
                'driver',
                'locomotiveNumber',
            ])
            ->get();

        return response(
            content: [
                'message' => 'Trains fetched successfully.',
                'trains' => TrainResource::collection(
                    resource: $trains,
                ),
            ],
            status: Http::OK(),
        );
    }
}
