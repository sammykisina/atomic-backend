<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\Line;

final class LineService
{
    /**
     * GET LINE WITH ID
     * @param int $line_id
     * @return Line|null
     */
    public static function getLineWithId(int $line_id): ?Line
    {
        return Line::query()->where('id', $line_id)
            ->with(['stations', 'regions'])
            ->first();
    }
    /**
     * CREATE LINE
     * @param string $name
     * @return Line
     */
    public function createLine(string $name): Line
    {
        return Line::query()->create([
            "name" => $name,
        ]);
    }

    /**
     * EDIT LINE
     * @param string $name
     * @param Line $line
     * @return bool
     */
    public function editLine(string $name, Line $line): bool
    {
        return $line->update(
            ['name' => $name],
        );
    }

    /**
     * CREATE LINE REGIONS
     * @param array $regions
     * @param Line $line
     * @return array[]
     */
    public function createLineRegions(array $regions, Line $line): array
    {
        return $line->regions()->sync($regions);
    }

    /**
     * Summary of createLineCounties
     * @param array $counties
     * @param Line $line
     * @return array[]
     */
    public function createLineCounties(array $counties, Line $line): array
    {
        return $line->counties()->sync($counties);
    }
}
