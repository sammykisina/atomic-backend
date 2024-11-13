<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Trains;

use Domains\SuperAdmin\Models\Train;
use Domains\SuperAdmin\Resources\TrainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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

        // if ($train->driver_id !== Auth::id()) {
        //     abort(
        //         code: Http::EXPECTATION_FAILED(),
        //         message: "Sorry. This Service Order may not be yours. Please double check or contact Trans Logic.",
        //     );
        // }

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
