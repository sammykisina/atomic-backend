<?php

declare(strict_types=1);

namespace Domains\Driver\Services;

use Domains\Driver\Enums\LicenseDirections;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Models\Location;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class JourneyService
{
    /**
     * GET JOURNEY DIRECTION TO DETERMINE LICENSE DIRECTION
     * @param float $origin
     * @param float $destination
     * @return LicenseDirections|null
     */
    public static function getJourneyDirection(float $origin, float $destination): LicenseDirections | null
    {
        $station_difference = $origin - $destination;

        if ($station_difference > 0) {
            return LicenseDirections::DOWN_TRAIN;
        }

        if ($station_difference < 0) {
            return LicenseDirections::UP_TRAIN;
        }

        return null;
    }

    /**
     * GET CURRENT USER ACTIVE JOURNEY
     * @return Journey|null
     */
    public static function activeJourney(): ?Journey
    {
        return Journey::query()
            ->where('is_active', true)
            ->whereHas(relation: 'train', callback: function ($query): void {
                $query->where('driver_id', Auth::id());
            })
            ->with(relations: [
                'train.line',
                'train.origin',
                'train.destination',
                'train.locomotiveNumber',
                'train.driver',
                'licenses',
                'licenses',
            ])
            ->first();
    }

    /**
     *
     */
    public static function getJourneyById(int $journey_id): Journey
    {
        return Journey::query()
            ->where('id', $journey_id)
            ->With(['train', 'licenses'])
            ->first();
    }

    /**
     * CREATE JOURNEY
     * @param array $journeyData
     * @return Journey
     */
    public function createJourney(array $journeyData): Journey
    {
        return Journey::query()->create(attributes: $journeyData);
    }

    /**
     * UPDATE JOURNEY
     * @param Journey $journey
     * @param array $updatedJourneyData
     * @return bool
     */
    public function editJourney(Journey $journey, array $updatedJourneyData): bool
    {
        return $journey->update([
            'train' => $updatedJourneyData['train'],
            'service_order' => $updatedJourneyData['service_order'],
            'number_of_wagons' => $updatedJourneyData['number_of_wagons'],
            'locomotive_number' => $updatedJourneyData['locomotive_number'],
            'tail_number' => $updatedJourneyData['tail_number'],
            'origin_station_id' => $updatedJourneyData['origin_station_id'],
            'line_id' => $updatedJourneyData['line_id'],
            'destination_station_id' => $updatedJourneyData['destination_station_id'],
        ]);
    }


    public function createLicense(Journey $journey): License
    {
        return License::query()->create([
            'journey_id' => $journey->id,
        ]);
    }

    /**
     * CREATE CURRENT TRAIN LOCATION
     * @param Journey $journey
     * @param array $updatedJourneyData
     * @return Location
     */
    public function createTrainLocation(Journey $journey, array $locationData): Location
    {
        $last_location = Location::query()->latest()->first();

        $location = Location::query()->create([
            'journey_id' => $journey->id,
            'station_id' => $locationData['station_id'] ?? null,
            'loop_id' => $locationData['loop_id'] ?? null,
            'section_id' => $locationData['section_id'] ?? null,
            'status' => true,

            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
        ]);

        if ($last_location && $location) {
            $this->disablePreviousLocation(location: $last_location);
        }

        return $location;
    }

    /**
     * DISABLE PREVIOUS LOCATION
     * @param Location $location
     * @return void
     */
    private function disablePreviousLocation(Location $location): void
    {
        $maxAttempts = 10;
        $delay = 1000;

        retry(times: $maxAttempts, callback: function () use ($location): void {
            DB::transaction(callback: function () use ($location): void {
                $is_updated = $location->update([
                    'status' => false,
                ]);

                if ( ! $is_updated) {
                    throw new Exception("Update failed");
                }
            });
        }, sleepMilliseconds: $delay);
    }
}
