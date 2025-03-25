<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Station;

final class StationService
{
    /**
     * GET STATION WITH ID
     * @param int $stationId
     * @return Station
     */
    public static function getStationById(int $station_id): Station
    {
        return Station::query()->where('id', $station_id)->first();
    }
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

            'start_latitude_top' => $stationData['start_latitude_top'],
            'start_longitude_top' => $stationData['start_longitude_top'],
            'start_latitude_bottom' => $stationData['start_latitude_bottom'],
            'start_longitude_bottom' => $stationData['start_longitude_bottom'],

            'end_latitude_top' => $stationData['end_latitude_top'],
            'end_longitude_top' => $stationData['end_longitude_top'],
            'end_latitude_bottom' => $stationData['end_latitude_bottom'],
            'end_longitude_bottom' => $stationData['end_longitude_bottom'],

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

            'start_latitude_top' => $updatedStationData['start_latitude_top'],
            'start_longitude_top' => $updatedStationData['start_longitude_top'],
            'start_latitude_bottom' => $updatedStationData['start_latitude_bottom'],
            'start_longitude_bottom' => $updatedStationData['start_longitude_bottom'],

            'end_latitude_top' => $updatedStationData['end_latitude_top'],
            'end_longitude_top' => $updatedStationData['end_longitude_top'],
            'end_latitude_bottom' => $updatedStationData['end_latitude_bottom'],
            'end_longitude_bottom' => $updatedStationData['end_longitude_bottom'],

            'line_id' => $updatedStationData['line_id'],
            'is_yard' => $updatedStationData['is_yard'],
            'position_from_line' => $updatedStationData['position_from_line'],
        ]);
    }
}
