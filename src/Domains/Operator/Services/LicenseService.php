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
