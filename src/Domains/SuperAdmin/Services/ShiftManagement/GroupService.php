<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services\ShiftManagement;

use Domains\SuperAdmin\Models\Group;

final class GroupService
{
    /**
     * GET GROUP WITH ID
     * @param int $group_id
     * @return Group | null
     */
    public static function getGroupById(int $group_id): ?Group
    {
        return Group::query()
            ->with(relations: [
                'desks',
            ])->where('id', $group_id)->first();
    }

    /**
     * CREATE GROUP
     * @param array $groupData
     * @return Group
     */
    public function createGroup(array $groupData): Group
    {
        return Group::query()->create(attributes: $groupData);
    }

    /**
     * EDIT GROUP
     * @param array $groupData
     * @return Group
     */
    public function editGroup(array $groupData, Group $group): bool
    {
        return $group->update(attributes: $groupData);
    }
}
