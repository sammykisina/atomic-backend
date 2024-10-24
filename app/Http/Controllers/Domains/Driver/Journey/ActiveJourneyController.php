<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ActiveJourneyController
{
    public function __invoke(Request $request): Response
    {
        $journey = JourneyService::activeJourney();

        return response(
            content: [
                'message' => 'Active Journey fetched successfully.',
                'journey' => $journey ? new JourneyResource(
                    resource: $journey,
                ) : null,
            ],
            status: Http::OK(),
        );
    }
}
