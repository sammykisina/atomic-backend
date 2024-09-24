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
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
    public function acceptRequest(LicenseRequest $request, Journey $journey, DatabaseNotification $notification): Response | HttpException
    {
        $license = DB::transaction(function () use ($request, $journey, $notification) {
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

            if ( ! in_array($request->validated('destination_station_id'), $currentShift->stations)) {
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

            $journey_direction = JourneyService::getJourneyDirection(
                origin: $journey->origin->start_kilometer,
                destination: $journey->destination->end_kilometer,
            );

            $license_origin_station = StationService::getStation(
                id: $request->validated(key: 'origin_station_id'),
            );

            $license_destination_station = StationService::getStation(
                id: $request->validated(key: 'destination_station_id'),
            );

            $license_direction =  JourneyService::getJourneyDirection(
                origin: $license_origin_station->start_kilometer,
                destination: $license_destination_station->start_kilometer,
            );


            if ($license_direction !== $journey_direction) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License direction does not match journey direction',
                );
            }

            $uniqueLicenseNumber = $this->licenseService->getLicense();

            $license = $this->licenseService->acceptJourneyRequest(
                licenseData: [
                    'license_number' => $uniqueLicenseNumber,
                    'journey_id' => $journey->id,
                    'origin_station_id' => $request->validated(key: 'origin_station_id'),
                    'destination_station_id' => $request->validated(key: 'destination_station_id'),
                    'main_id' => $request->validated(key: 'stop_at_main_line') ? $request->validated(key: 'origin_station_id') : null,
                    'loop_id' => ! $request->validated(key: 'stop_at_main_line') ? $request->validated(key: 'loop_id') : null,
                    'section_id' => $request->validated(key: 'section_id'),
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
                'license' => new LicenseResource(
                    resource: $license,
                ),
                'message' => 'Request accepted and license assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
