<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Trains;

use Domains\SuperAdmin\Models\Train;
use Domains\SuperAdmin\Resources\TrainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class SearchController
{
    public function __invoke(Request $request): Response
    {
        $train  = QueryBuilder::for(subject: Train::class)
            ->where('service_order', $request->query('service_order'))
            ->allowedIncludes(includes: [
                'line',
                'origin',
                'destination',
                'driver',
                'locomotiveNumber',
                'journey',
            ])
            ->first();

        if ($train && $train->journey) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: "This Service Order was used in another journey. Double check the Service Order or Contact OCC.",
            );
        }

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
