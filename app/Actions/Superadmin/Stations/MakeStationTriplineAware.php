<?php

declare(strict_types=1);

namespace App\Actions\Superadmin\Stations;

use Domains\SuperAdmin\Models\Station;
use Illuminate\Support\Facades\DB;

final class MakeStationTriplineAware
{
    public function handle(Station $station): bool
    {
        return DB::transaction(function () use ($station): bool {
            $station->section->update([
                'station_id' => null,
            ]);

            return $station->update(attributes: [
                'has_trip_line' => true,
            ]);
        });
    }
}
