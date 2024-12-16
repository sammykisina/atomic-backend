<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Obc;

final class ObcService
{
    /**
     * GET OBC WITH ID
     * @param int $obc_id
     * @return Obc | null
     */
    public static function getObcById(int $obc_id): ?Obc
    {
        return Obc::query()
            ->where('id', $obc_id)
            ->with(relations: ['locomotiveNumber'])
            ->first();
    }

    /**
     * CREATE OBC
     * @param array $obcData
     * @return Obc
     */
    public function createObc(array $obcData): Obc
    {
        return Obc::query()->create(attributes: $obcData);
    }

    /**
     * EDIT OBC
     * @param array $updatedObcData
     * @return bool
     */
    public function editObc(array $updatedObcData, Obc $obc): bool
    {
        return $obc->update(attributes: $updatedObcData);
    }
}
