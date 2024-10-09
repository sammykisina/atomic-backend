<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Models\Journey;
use Domains\Driver\Resources\JourneyResource;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $journeys = QueryBuilder::for(Journey::class)
            ->where('driver_id', Auth::id())
            ->with([
                'licenses.paths.originStation',
                'licenses.paths.destinationStation',
            ])
            ->get();

        $stationIds = [];

        foreach ($journeys as $journey) {
            foreach ($journey->licenses as $license) {
                foreach ($license->paths as $path) {
                    if (isset($path['originStation']['id'])) {
                        $stationIds[] = $path['originStation']['id'];
                    }

                    if (isset($path['destinationStation']['id'])) {
                        $stationIds[] = $path['destinationStation']['id'];
                    }
                }
            }
        }

        $stationIds = array_unique($stationIds);
        sort($stationIds);

        $stations = Station::whereIn('id', $stationIds)->with(['loops', 'section'])->get();

        $updatedJourneys = $journeys->map(function ($journey) use ($stations) {
            $journey->licenses = $journey->licenses->map(function ($license) use ($stations) {
                $license->stations = $stations;
                unset($license->paths);
                return $license;
            });

            return $journey;
        });


        return response(
            content: [
                'message' => 'Journey fetched successfully.',
                'journey' => JourneyResource::collection(
                    resource: $updatedJourneys,
                ),
            ],
            status: Http::OK(),
        );
    }
}
