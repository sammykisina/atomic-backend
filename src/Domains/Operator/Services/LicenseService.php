<?php

declare(strict_types=1);

namespace Domains\Operator\Services;

use Carbon\Carbon;
use Domains\Driver\Models\License;
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
        return License::query()->create([
            'license_number' => $licenseData['license_number'],
            'journey_id' => $licenseData['journey_id'],
            'origin_station_id' => $licenseData['origin_station_id'],
            'destination_station_id' => $licenseData['destination_station_id'],
            'main_id' => $licenseData['main_id'] ?? null,
            'loop_id' => $licenseData['loop_id'] ?? null,
            'section_id' => $licenseData['section_id'],
            'direction' => $licenseData['direction'],
            'issuer_id' => Auth::id(),
            'issued_at' => Carbon::now(),
        ]);
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
