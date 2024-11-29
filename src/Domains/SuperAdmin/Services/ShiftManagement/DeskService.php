<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services\ShiftManagement;

use Domains\SuperAdmin\Models\Desk;

final class DeskService
{
    public static function getDeskById(int $desk_id): ?Desk
    {
        return Desk::query()
            ->where('id', $desk_id)
            ->with([
                'group',
            ])
            ->first();
    }
    /**
     * CREATE DESK
     * @param array $deskData
     * @return Desk
     */
    public function createDesk(array $deskData): Desk
    {
        return Desk::query()->create(attributes: $deskData);
    }

    /**
     * EDIT DESK
     * @param array $updatedDeskData
     * @param Desk $desk
     * @return bool
     */
    public function editDesk(array $updatedDeskData, Desk $desk): bool
    {
        return $desk->update(attributes: $updatedDeskData);
    }
}
