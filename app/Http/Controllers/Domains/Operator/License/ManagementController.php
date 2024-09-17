<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\License;

use Domains\Driver\Models\Journey;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Resources\LicenseResource;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Services\StationService;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    public function __construct(
        protected LicenseService $licenseService,
    ) {}

    /**
     * ACCEPT JOURNEY REQUEST
     * @param Journey $journey
     * @return mixed
     */
    public function acceptJourney(LicenseRequest $request, Journey $journey)
    {
        $uniqueLicenseNumber = $this->licenseService->getLicense();

        $originStation = StationService::getStation(
            id: $request->validated(key: 'origin_station_id'),
        );

        $destinationStation = StationService::getStation(
            id: $request->validated(key: 'destination_station_id'),
        );

        $license = $this->licenseService->acceptJourneyRequest(
            licenseData: [
                'license_number' => $uniqueLicenseNumber,
                'journey_id' => $journey->id,
                'origin_station_id' => $request->validated(key: 'origin_station_id'),
                'destination_station_id' => $request->validated(key: 'destination_station_id'),
                'main_id' => $request->validated(key: 'stop_at_main_line') ? $request->validated(key: 'origin_station_id') : null,
                'loop_id' => ! $request->validated(key: 'stop_at_main_line') ? $request->validated(key: 'loop_id') : null,
                'section_id' => $request->validated(key: 'section_id'),
                'direction' =>  JourneyService::getJourneyDirection(
                    origin: $originStation->start_kilometer,
                    destination: $destinationStation->start_kilometer,
                ),
            ],
        );

        if ( ! $license) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This journey was not accepted.Please try again.',
            );
        }

        $journey->driver->notify(new LicenseNotification(
            license: $license,
            type: NotificationTypes::JOURNEY_ACCEPTED,
        ));

        return response(
            content: [
                'license' => new LicenseResource(
                    resource: $license,
                ),
                'message' => 'Journey accepted successfully.',
            ],
            status: Http::CREATED(),
        );
    }

}
