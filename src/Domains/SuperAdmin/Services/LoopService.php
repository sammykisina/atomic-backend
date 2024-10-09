<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Station;

final class LoopService
{
    /**
     * GET LOOP BY ID
     * @param int $loop_id
     * @return Loop
     */
    public static function getLoopById(int $loop_id): Loop
    {
        return Loop::query()->where('id', $loop_id)->first();
    }
    /**
     * CREATE LOOP
     * @param array $loopData
     * @return Loop
     */
    public function createLoop(array $loopData, Line $line, Station $station): Loop
    {
        return Loop::query()->create([
            'position' => $loopData['position'],
            'distance' => $loopData['distance'],
            'station_id' => $station->id,

            'start_latitude_top' => $loopData['start_latitude_top'],
            'start_longitude_top' => $loopData['start_longitude_top'],
            'start_latitude_bottom' => $loopData['start_latitude_bottom'],
            'start_longitude_bottom' => $loopData['start_longitude_bottom'],

            'end_latitude_top' => $loopData['end_latitude_top'],
            'end_longitude_top' => $loopData['end_longitude_top'],
            'end_latitude_bottom' => $loopData['end_latitude_bottom'],
            'end_longitude_bottom' => $loopData['end_longitude_bottom'],

            'line_id' => $line->id,
        ]);
    }

    /**
     * EDIT LOOP
     * @param array $updatedLoopData
     * @param Loop $loop
     * @return bool
     */
    public function editLoop(array $updatedLoopData, Loop $loop, Station $station): bool
    {
        return $loop->update([
            'position' => $updatedLoopData['position'],
            'distance' => $updatedLoopData['distance'],

            'start_latitude_top' => $updatedLoopData['start_latitude_top'],
            'start_longitude_top' => $updatedLoopData['start_longitude_top'],
            'start_latitude_bottom' => $updatedLoopData['start_latitude_bottom'],
            'start_longitude_bottom' => $updatedLoopData['start_longitude_bottom'],

            'end_latitude_top' => $updatedLoopData['end_latitude_top'],
            'end_longitude_top' => $updatedLoopData['end_longitude_top'],
            'end_latitude_bottom' => $updatedLoopData['end_latitude_bottom'],
            'end_longitude_bottom' => $updatedLoopData['end_longitude_bottom'],

            'station_id' => $station->id,
        ]);
    }
}
