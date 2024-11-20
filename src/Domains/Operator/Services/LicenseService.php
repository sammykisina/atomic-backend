<?php

declare(strict_types=1);

namespace Domains\Operator\Services;

use Carbon\Carbon;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Services\LoopService;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class LicenseService
{
    /**
     * GET MODEL
     * @param string $model_type
     * @param int $model_id
     * @return Station|Loop|Section
     */
    public static function getModel(string $model_type, int $model_id): Station | Loop | Section
    {
        return  match ($model_type) {
            'STATION' =>  StationService::getStationById(station_id: $model_id),
            'SECTION' =>  SectionService::getSectionById(section_id: $model_id),
            'LOOP' =>  LoopService::getLoopById(loop_id: $model_id),
        };
    }


    /**
     * GET LICENSE ORIGIN
     * @param License $license
     * @return string
     */
    public static function getLicenseOrigin(Model $model): string
    {
        return  match (get_class(object: $model)) {
            Station::class =>  $model->name,
            Loop::class =>  $model->station->name . ' - LOOP',
            Section::class =>  $model->start_name . ' - ' . $model->end_name,
        };

    }

    /**
     * GET LICENSE BY ID
     * @param int $license_id
     * @return License
     */
    public static function getLicenseById(int $license_id): ?License
    {
        return License::query()
            ->where('id', $license_id)
            ->with(relations: ['journey.train.driver', 'issuer'])
            ->first();
    }

    /**
     * GET PREVIOUS LATEST LICENSE
     * @param Journey $journey
     * @return License
     */
    public static function getPrevLatestLicense(Journey $journey): ?License
    {
        return License::query()
            ->where('journey_id', $journey->id)
            ->where('status', LicenseStatuses::CONFIRMED->value)
            ->latest()->first();
    }

    /**
     * CREATE LICENSE AFTER MATHS
     * @param License $license
     * @param Journey $journey
     * @return void
     */
    public static function createLicenseAfterMaths(License $license, Journey $journey): void
    {
        $license->update(attributes: [
            'logs' => [
                [
                    'type' => AtomikLogsTypes::LICENSE_CREATED->value,
                    'created_at' => $license->created_at,
                    'created_by' => Auth::user()->employee_id,
                ],
            ],
        ]);

        AtomikLogService::createAtomicLog(
            atomikLogData: [
                'type' => AtomikLogsTypes::LICENSE_CREATED->value,
                'resourceble_id' => $license->id,
                'resourceble_type' => License::class,
                'actor_id' => Auth::id(),
                'current_location' => '',
                'train_id' => $journey->train_id,
                'receiver_id' => $journey->train->driver_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ],
        );
    }
    /**
     * GENERATE A UNIQUE RANDOM LICENSE NUMBER
     * @return string
     */
    public function getLicense(): string
    {
        $variations = [1, 2, 3, 4, 5];
        $randomElement = $variations[array_rand(array: $variations)];
        $uniqueLicenseNumber = $this->generateUniqueLicenseNumber(
            length: $randomElement,
        );

        return $uniqueLicenseNumber;
    }

    /**
     * ACCEPT JOURNEY REQUEST
     * @param array $licenseData
     * @return License
     */
    public function createJourneyLicense(array $licenseData): License
    {
        $license =  License::query()->create(array_merge(
            $licenseData,
            [
                'issuer_id' => Auth::id(),
                'issued_at' => Carbon::now(),
            ],
        ));

        return $license;
    }

    /**
     * GENERATE LICENSE NUMBER
     * @param mixed $length
     * @return string
     */
    private function generateUniqueLicenseNumber(int $length = 5): string
    {
        do {
            $licenseNumber = $this->generateRandomString($length);
            $existingLicense = License::where('license_number', $licenseNumber)->first();

        } while ($existingLicense);

        return $licenseNumber;
    }

    /**
     * ACTUAL NUMBER
     * @param mixed $length
     * @return string
     */
    private function generateRandomString(int $length = 5): string
    {
        // You can use a predefined set of characters or customize as per your requirement
        $characters = '123456789';
        $charactersLength = mb_strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
