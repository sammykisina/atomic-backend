<?php

declare(strict_types=1);

namespace Domains\Operator\Services;

use Carbon\Carbon;
use Domains\Driver\Models\License;
use Domains\Driver\Models\Path;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Services\LoopService;
use Domains\SuperAdmin\Services\SectionService;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Support\Facades\Auth;

final class LicenseService
{
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
    public function acceptJourneyRequest(array $licenseData): License
    {
        $license =  License::query()->create([
            'license_number' => $licenseData['license_number'],
            'journey_id' => $licenseData['journey_id'],
            'direction' => $licenseData['direction'],
            'issuer_id' => Auth::id(),
            'issued_at' => Carbon::now(),
        ]);

        foreach ($licenseData['path'] as $path) {
            $new_path = Path::query()->create([
                'license_id' => $license->id,
                'origin_station_id' => $path['origin_station_id'],
                'origin_main_id' => $path['origin_main_id'],
                'origin_loop_id' => $path['origin_loop_id'],

                'section_id' => $path['section_id'],

                'destination_station_id' => $path['destination_station_id'],
                'destination_main_id' => $path['destination_main_id'],
                'destination_loop_id' => $path['destination_loop_id'],
            ]);



            if ($path['originate_from_main_line']) {
                $origin_station = StationService::getStationById(
                    station_id: $new_path->origin_station_id,
                );


                $origin_station->update([
                    'status' => StationSectionLoopStatuses::LICENSE_ISSUED,
                ]);
            } else {
                $origin_loop = LoopService::getLoopById(
                    loop_id: $new_path->origin_loop_id,
                );

                $origin_loop->update([
                    'status' => StationSectionLoopStatuses::LICENSE_ISSUED,
                ]);
            }

            if ($path['section_id']) {
                $section = SectionService::getSectionById(
                    section_id: $new_path->section_id,
                );

                $section->update([
                    'status' => StationSectionLoopStatuses::LICENSE_ISSUED,
                ]);
            }

            if ($path['stop_at_main_line']) {
                $destination_station = StationService::getStationById(
                    station_id: $new_path->destination_station_id,
                );

                $destination_station->update([
                    'status' => StationSectionLoopStatuses::LICENSE_ISSUED,
                ]);
            } else {
                $destination_loop = LoopService::getLoopById(
                    loop_id: $new_path->destination_loop_id,
                );

                $destination_loop->update([
                    'status' => StationSectionLoopStatuses::LICENSE_ISSUED,
                ]);
            }
        }

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
