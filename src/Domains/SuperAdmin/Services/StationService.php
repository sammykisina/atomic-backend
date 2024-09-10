<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Station;

final class StationService
{
    /**
     * CREATE STATION
     * @param array $stationData
     * @return Station
     */
    public function createStation(array $stationData): Station
    {
        return Station::query()->create([
            'name' => $stationData['name'],
            'start_kilometer' => $stationData['start_kilometer'],
            'end_kilometer' => $stationData['end_kilometer'],
            'start_latitude' => $stationData['start_latitude'],
            'start_longitude' => $stationData['start_longitude'],
            'end_latitude' => $stationData['end_latitude'],
            'end_longitude' => $stationData['end_longitude'],
            'line_id' => $stationData['line_id'],
            'is_yard' => $stationData['is_yard'],
            'position_from_line' => $stationData['position_from_line'],
        ]);
    }

    /**
     * EDIT STATION
     * @param array $updatedStationData
     * @param Station $station
     * @return bool
     */
    public function editStation(array $updatedStationData, Station $station): bool
    {
        return $station->update([
            'name' => $updatedStationData['name'],
            'start_kilometer' => $updatedStationData['start_kilometer'],
            'end_kilometer' => $updatedStationData['end_kilometer'],
            'start_latitude' => $updatedStationData['start_latitude'],
            'start_longitude' => $updatedStationData['start_longitude'],
            'end_latitude' => $updatedStationData['end_latitude'],
            'end_longitude' => $updatedStationData['end_longitude'],
            'line_id' => $updatedStationData['line_id'],
            'is_yard' => $updatedStationData['is_yard'],
            'position_from_line' => $updatedStationData['position_from_line'],
        ]);
    }
}
