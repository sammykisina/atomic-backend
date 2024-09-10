<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Station;

final class LoopService
{
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
            'station_id' => $station->id,
        ]);
    }
}
