<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Services;

use Domains\RegionAdmin\Models\Region;

final class RegionService
{
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
