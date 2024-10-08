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

final class ActiveJourneyController
{
    public function __invoke(Request $request): Response
    {
        $journey  = QueryBuilder::for(subject: Journey::class)
            ->where('driver_id', Auth::id())
            ->where('status', true)
            ->allowedIncludes(includes: ['origin', 'destination', 'licenses.section', 'licenses.originStation', 'licenses.destinationStation', 'licenses.main', 'licenses.loop'])
            ->first();

        return response(
            content: [
                'message' => 'Active Journey fetched successfully.',
                'journeys' => new JourneyResource(
                    resource: $journey,
                ),
            ],
            status: Http::OK(),
        );
    }
}
