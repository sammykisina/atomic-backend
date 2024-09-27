<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Region;

final class RegionService
{
    /**
     * GET REGION WITH ID
     * @param int $region_id
     * @return Region|null
     */
    public static function getRegionWithId(int $region_id): ?Region
    {
        return Region::query()
            ->where('id', $region_id)
            ->first();
    }
    /**
     * CREATE REGION
     * @param array $regionData
     * @return Region
     */
    public function createRegion(array $regionData): Region
    {
        return Region::query()->create([
            'name' => $regionData['name'],
        ]);
    }

    /**
     * EDIT REGION
     * @param array $updatedRegionData
     * @param Region $region
     * @return bool
     */
    public function editRegion(array $updatedRegionData, Region $region): bool
    {
        return $region->update([
            'name' => $updatedRegionData['name'],
        ]);
    }
}
