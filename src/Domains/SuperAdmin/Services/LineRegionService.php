<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Line;

final class LineRegionService
{
    public function createLineRegionsDivisions(array $lineRegionsData, Line $line): array
    {
        $syncData = [];

        foreach ($lineRegionsData as $lineRegion) {
            $syncData[$lineRegion['region_id']] = [
                'start_station_id' => $lineRegion['start_station_id'],
                'end_station_id' => $lineRegion['end_station_id'],
            ];
        }

        return $line->regions()->sync($syncData);
    }
}
