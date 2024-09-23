<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Licenses;

use Domains\Driver\Models\Journey;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Resources\LicenseResource;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
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
    public function acceptRequest(LicenseRequest $request, Journey $journey, DatabaseNotification $notification)
    {
        $license = DB::transaction(function () use ($request, $journey, $notification) {
            $currentShift = Shift::where('shift_start_station_id', '<=', $request->validated('origin_station_id'))
                ->where('shift_end_station_id', '>=', $request->validated('origin_station_id'))
                ->first();

            dd($request->validated('destination_station_id'));
            dd($currentShift->shift_end_station_id);


            if ( ! $currentShift || $request->validated('destination_station_id') > $currentShift->shift_end_station_id) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Destination station is outside of the current shift\'s control.',
                );
            }


            if (null !== $notification->read_at) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
                );
            }

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
                    message: 'License was not created. Please try again.',
                );
            }

            $journey->driver->notify(new LicenseNotification(
                license: $license,
                type: NotificationTypes::REQUEST_ACCEPTED,
            ));


            $notification->markAsRead();

            return $license;

        });

        return response(
            content: [
                'license' => new LicenseResource(
                    resource: $license,
                ),
                'message' => 'Request accepted and license assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
