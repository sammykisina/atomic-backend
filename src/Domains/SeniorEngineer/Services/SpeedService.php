<?php

declare(strict_types=1);

namespace Domains\SeniorEngineer\Services;

use Domains\SeniorEngineer\Models\Speed;

final class SpeedService
{
    /**
     * CHECK FOR OVERLAPPING SPEEDS
     * @param float $startKm
     * @param float $endKm
     * @param int $line_id
     * @return Speed | null
     */
    public static function checkOverlap(float $startKm, float $endKm, int $line_id): Speed|null
    {
        return Speed::where('line_id', $line_id)
            ->where(function ($query) use ($startKm, $endKm): void {
                $query->whereBetween('start_kilometer', [$startKm, $endKm])
                    ->orWhereBetween('end_kilometer', [$startKm, $endKm])
                    ->orWhereRaw('? BETWEEN start_kilometer AND end_kilometer', [$startKm])
                    ->orWhereRaw('? BETWEEN start_kilometer AND end_kilometer', [$endKm]);
            })->first();
    }
    /**
     * CREATE SPEED
     * @param array $speedData
     * @return Speed
     */
    public function createSpeed(array $speedData): Speed
    {
        return Speed::query()->create(attributes: $speedData);
    }
}
