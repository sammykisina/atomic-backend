<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services\ShiftManagement;

use Domains\SuperAdmin\Models\Shift;

final class ShiftService
{
    /**
     * GET SHIFT WITH ID
     * @param int $shift_id
     * @return Shift | null
     */
    public static function getShiftById(int $shift_id): ?Shift
    {
        return Shift::query()
            ->where('id', $shift_id)
            ->with(['user', 'desk.group'])
            ->first();
    }
    /**
     * CREATE SHIFT
     * @param array $shiftData
     * @return Shift
     */
    public function createShift(array $shiftData): Shift
    {
        return Shift::query()->create(attributes: $shiftData);
    }

    /**
     * EDIT SHIFT
     * @param array $shiftData
     * @return bool
     */
    public function editShift(array $shiftData, Shift $shift): bool
    {
        return $shift->update(
            attributes: $shiftData,
        );
    }
}
