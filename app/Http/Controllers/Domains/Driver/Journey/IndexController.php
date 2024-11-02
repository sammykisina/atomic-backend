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
        $journeys = QueryBuilder::for(subject: Journey::class)
            ->whereHas(relation: 'train', callback: function ($query): void {
                $query->where('driver_id', Auth::id());
            })
            ->with(relations: [
                'train.line',
                'train.origin',
                'train.destination',
                'train.locomotiveNumber',
                'train.driver',
                'licenses',
                'licenses',
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
