<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Shift;
use Illuminate\Support\Facades\Auth;

final class ShiftService
{
    /**
     * CREATE SHIFT
     * @param array $shiftData
     * @return Shift
     */
    public function createShift(array $shiftData, array $stations): Shift
    {
        return Shift::query()->create([
            'desk_id' => $shiftData['desk_id'],
            'user_id' => $shiftData['user_id'],
            'shift_start_station_id' => $shiftData['shift_start_station_id'],
            'shift_end_station_id' => $shiftData['shift_end_station_id'],
            'day' => $shiftData['day'],
            'from' => $shiftData['from'],
            'to' => $shiftData['to'],
            'line_id' => $shiftData['line_id'],
            'stations' => $stations,
            'creator_id' => Auth::id(),
        ]);
    }
}
