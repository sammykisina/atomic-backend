<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Licenses;

use Domains\Driver\Models\Journey;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Resources\LicenseResource;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Services\StationService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
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
        DB::transaction(function () use ($request, $journey, $notification) {
            $currentShift = Shift::query()
                ->where("status", ShiftStatuses::CONFIRMED)
                ->where("active", true)
                ->where('user_id', Auth::id())
                ->first();

            if ( ! $currentShift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Opps! You don`t have an active shift to accept this request. Please contact your system administrator.',
                );
            }

            foreach ($request->validated('path') as $key => $path) {
                if ( ! in_array($path['destination_station_id'], $currentShift->stations)) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: "Destination station as position " . $key + 1 . " is outside of the current shift\'s control.",
                    );
                }
            }


            if (null !== $notification->read_at) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
                );
            }


            $journey_direction = JourneyService::getJourneyDirection(
                origin: $journey->origin->start_kilometer,
                destination: $journey->destination->end_kilometer,
            );

            foreach ($request->validated('path') as $key => $path) {
                $license_origin_station = StationService::getStation(
                    id: $path['origin_station_id'],
                );

                $license_destination_station = StationService::getStation(
                    id: $path['destination_station_id'],
                );

                $license_direction =  JourneyService::getJourneyDirection(
                    origin: $license_origin_station->start_kilometer,
                    destination: $license_destination_station->start_kilometer,
                );


                if ($license_direction !== $journey_direction) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: "License path direction at position " . $key + 1 . " does not match journey direction",
                    );
                }
            }

            $path_data = [];
            foreach ($request->validated('path') as $path) {
                $path_data[] =  [
                    'origin_station_id' => $path['origin_station_id'],
                    'originate_from_main_line' => $path['originate_from_main_line'],
                    'origin_main_id' =>  $path['originate_from_main_line'] ? $path['origin_station_id'] : null,
                    'origin_loop_id' => $path['originate_from_main_line'] ? null : $path['origin_loop_id'],

                    'section_id' => $path['section_id'] ?? null,

                    'destination_station_id' => $path['destination_station_id'],
                    'stop_at_main_line' => $path['stop_at_main_line'],
                    'destination_main_id' => $path['stop_at_main_line'] ? $path['destination_station_id'] : null,
                    'destination_loop_id' => $path['stop_at_main_line'] ? null : $path['destination_loop_id'],
                ];
            }

            // dd($path_data);

            $uniqueLicenseNumber = $this->licenseService->getLicense();

            $license = $this->licenseService->acceptJourneyRequest(
                licenseData: [
                    'license_number' => $uniqueLicenseNumber,
                    'journey_id' => $journey->id,
                    'path' => $path_data,
                    'direction' =>  $license_direction,
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
                'message' => 'Request accepted and license assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
