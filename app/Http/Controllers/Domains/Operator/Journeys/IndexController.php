<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

use Domains\Driver\Models\Journey;
use Domains\Driver\Resources\JourneyResource;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $journeys = QueryBuilder::for(Journey::class)
            ->with([
                'licenses.paths.originStation',
                'licenses.paths.destinationStation',
                'driver',
            ])
            ->allowedFilters([AllowedFilter::exact('status')])
            ->get();

        $updatedJourneys = $journeys->map(function ($journey) {
            // Map each license individually
            $journey->licenses = $journey->licenses->map(function ($license) {
                $stationIds = [];

                // Collect station IDs from the license paths
                foreach ($license->paths as $path) {
                    if (isset($path['originStation']['id'])) {
                        $stationIds[] = $path['originStation']['id'];
                    }

                    if (isset($path['destinationStation']['id'])) {
                        $stationIds[] = $path['destinationStation']['id'];
                    }
                }

                // Get unique station IDs for this license
                $stationIds = array_unique($stationIds);
                sort($stationIds);

                // Fetch stations for this specific license
                $license->stations = Station::whereIn('id', $stationIds)->with(['loops', 'section'])->get();

                // Optionally remove paths from the license object if not needed
                unset($license->paths);

                return $license;
            });

            return $journey;
        });


        return response(
            content: [
                'message' => 'Journeys fetched successfully.',
                'journeys' => JourneyResource::collection(
                    resource: $updatedJourneys,
                ),
            ],
            status: Http::OK(),
        );
    }
}
