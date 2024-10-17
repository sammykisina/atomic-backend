<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Services;

use Domains\SuperAdmin\Models\LocomotiveNumber;

final class LocomotiveNumberService
{
    /**
     * GET LOCOMOTIVE NUMBER WITH NUMBER
     * @param string $number
     * @return LocomotiveNumber|null
     */
    public static function getLocomotiveNumberWithNumber(string $number): ?LocomotiveNumber
    {
        return LocomotiveNumber::query()->where('number', $number)->first();
    }

    /**
     * CREATE LOCOMOTIVE NUMBER
     * @param string $number
     * @return LocomotiveNumber
     */
    public function createLocomotiveNumber(string $number): LocomotiveNumber
    {
        return LocomotiveNumber::query()->create([
            "number" => $number,
        ]);
    }

    /**
     * EDIT LOCOMOTIVE NUMBER
     * @param string $number
     * @param LocomotiveNumber $locomotiveNumber
     * @return bool
     */
    public function editLocomotiveNumber(string $number, LocomotiveNumber $locomotiveNumber): bool
    {
        return $locomotiveNumber->update(
            ['number' => $number],
        );
    }
}
