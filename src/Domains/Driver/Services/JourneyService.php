<?php

declare(strict_types=1);

namespace Domains\Driver\Services;

use Domains\Driver\Enums\LicenseDirections;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Models\Location;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Exception;
use Illuminate\Database\Eloquent\Model;
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
     * GET JOURNEY BY ID
     * @param int $journey_id
     * @return Journey
     */
    public static function getJourneyById(int $journey_id): Journey
    {
        return Journey::query()
            ->where('id', $journey_id)
            ->With(['train', 'licenses'])
            ->first();
    }

    /**
     * FIND THE EXACT POINT A TRAIN IS
     * @param Journey $journey
     * @return array | null
     */
    public static function getTrainLocation(Journey $journey): array|null
    {
        $latest_train_license = License::query()
            ->where('journey_id', $journey->id)
            ->where('status', LicenseStatuses::CONFIRMED->value)
            ->first();


        if ( ! $latest_train_license) {
            return null;
        }

        if ($latest_train_license['train_at_origin']) {
            return [
                'id' => $latest_train_license['origin']['id'],
                'type' => $latest_train_license['origin']['type'],
            ];
        }

        foreach ($latest_train_license['through'] as $through) {
            if ($through['train_is_here']) {
                return [
                    'id' => $through['id'],
                    'type' => $through['type'],
                ];
            }
        }

        if ($latest_train_license['train_at_destination']) {
            return [
                'id' => $latest_train_license['destination']['id'],
                'type' => $latest_train_license['destination']['type'],
            ];
        }

        return null;
    }


    /**
     * GET LOCATION
     * @param Model $model
     * @return string
     */
    public static function getLocation(Model $model): string
    {
        return  match (get_class(object: $model)) {
            Station::class =>  $model->name,
            Loop::class =>  $model->station->name . ' - LOOP',
            Section::class =>  $model->start_name . ' - ' . $model->end_name,
        };

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
        return $journey->update(attributes: [
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
        return License::query()->create(attributes: [
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

        $location = Location::query()->create(attributes: [
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
