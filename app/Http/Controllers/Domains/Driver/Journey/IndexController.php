<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Models\Journey;
use Domains\Driver\Resources\JourneyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $journeys  = QueryBuilder::for(Journey::class)
            ->where('driver_id', Auth::id())
            ->allowedIncludes('origin', 'destination', 'licenses.section', 'licenses.originStation', 'licenses.destinationStation', 'licenses.main', 'licenses.loop')
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
