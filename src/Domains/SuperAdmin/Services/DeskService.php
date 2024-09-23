<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Desk;

final class DeskService
{
    /**
     * CREATE DESK
     * @param array $deskData
     * @return Desk
     */
    public function createDesk(array $deskData): Desk
    {
        return Desk::query()->create([
            'name' => $deskData['name'],
        ]);
    }

    /**
     * EDIT DESK
     * @param array $updatedDeskData
     * @param Desk $desk
     * @return bool
     */
    public function editDesk(array $updatedDeskData, Desk $desk): bool
    {
        return $desk->update([
            'name' => $updatedDeskData['name'],
        ]);
    }
}
