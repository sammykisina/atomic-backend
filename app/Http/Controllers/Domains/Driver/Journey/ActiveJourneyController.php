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

final class ActiveJourneyController
{
    public function __invoke(Request $request): Response
    {
        $journey = QueryBuilder::for(subject: Journey::class)
            ->where('driver_id', Auth::id())
            ->where('status', true)
            ->with([
                'licenses.paths.originStation',
                'licenses.paths.destinationStation',
                'origin',
                'destination',
            ])
            ->first();

        if ($journey) {
            $journey->licenses = $journey->licenses->map(function ($license) {
                $stationIds = [];

                // Collect station IDs from the license's paths
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

                // Optionally remove paths if no longer needed
                unset($license->paths);

                return $license;
            });
        }


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
