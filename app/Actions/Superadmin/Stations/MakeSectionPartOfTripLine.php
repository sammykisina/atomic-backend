<?php

declare(strict_types=1);

namespace App\Actions\Superadmin\Stations;

use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;

final class MakeSectionPartOfTripLine
{
    public function handle(Station $station, Section $section): bool
    {
        // 1 - Check if the station is trip-line aware
        if ( ! $station->has_trip_line) {
            abort(code: 417, message: 'Station is not trip-line aware. Please make it trip-line aware first.');
        }

        // 2 - Check if the section is the stating section of a station
        if ($section->station) {
            abort(code: 417, message: 'Section is already part of a station. Please remove it from the station first.');
        }

        // 3 - Ensure this section is not part of another trip-line pair
        if ($section->triplinestation_id) {
            abort(code: 417, message: 'Section is already part of a trip-line pair. Please remove it from the trip-line pair first.');
        }

        // 4 - Connect the section to the station
        return $section->update([
            'triplinestation_id' => $station->id,
        ]);
    }
}
