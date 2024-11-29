<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Models\Journey;
use Domains\Driver\Resources\JourneyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class ActiveJourneysController
{
    public function __invoke(Request $request): Response
    {
        $journeys = QueryBuilder::for(subject: Journey::class)
            ->where('is_active', true)
            ->with(relations: [
                'train.trainName',
                'train.line',
                'train.origin',
                'train.destination',
                'train.locomotiveNumber',
                'train.driver',
                'licenses.issuer',
                'licenses.rejector',
            ])
            ->get();


        return response(
            content: [
                'message' => 'Journeys fetched successfully.',
                'journey' => JourneyResource::collection(
                    resource: $journeys,
                ),
            ],
            status: Http::OK(),
        );
    }
}
