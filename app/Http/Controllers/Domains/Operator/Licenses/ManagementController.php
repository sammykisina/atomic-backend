<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Licenses;

use Domains\Driver\Enums\LicenseRouteStatuses;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Services\TrainService;
use Illuminate\Http\Request;
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
     *  ACCEPT JOURNEY REQUEST
     * @param LicenseRequest $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response | HttpException
     */
    public function acceptRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $is_authorized =  DB::transaction(function () use ($journey, $notification): bool {
            $is_updated = $journey->update(attributes: [
                'is_authorized' => true,
            ]);

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO3,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $journey->train->driver_id,
                'current_location' => $journey->train->origin->name,
                'train_id' => $journey->train_id,
            ]));


            $notification->markAsRead();

            return $is_updated;

        });

        if ( ! $is_authorized) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Request for line entry is not authorized successfully.Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Request for line entry has been authorized successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ASSIGN LICENSE
     * @param LicenseRequest $request
     * @param Journey $journey
     * @return  Response|HttpException
     */
    public function assignLicense(LicenseRequest $request, Journey $journey): Response|HttpException
    {
        DB::transaction(function () use ($request, $journey): License {
            $shift  = Shift::query()
                ->where('user_id', Auth::id())
                ->where('status', ShiftStatuses::CONFIRMED->value)
                ->where('active', true)
                ->first();

            $currentShiftIds = $journey->shifts ?? [];
            $currentShiftIds[] = $shift->id;

            $prev_latest_license = LicenseService::getPrevLatestLicense(
                journey: $journey,
            );

            if ($prev_latest_license) {
                $prev_latest_license->update(attributes: [
                    'status' => LicenseStatuses::USED->value,
                ]);
            }

            $license = $this->createLicense(
                request: $request,
                journey: $journey,
            );

            $journey->update(attributes: [
                'shifts' => $currentShiftIds,
            ]);

            return $license;
        });


        return response(
            content: [
                'message' => 'License assigned successfully. Awaiting confirmation.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * DECLINE LINE ENTRY
     * @param Request $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function declineRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $is_declined =  DB::transaction(function () use ($journey, $notification): bool {
            $is_declined = $journey->delete();

            $notification->markAsRead();

            return $is_declined;

        });

        if ( ! $is_declined) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Request for line entry is not declined successfully.Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Request for line entry has been authorized successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * CREATE LICENSE
     * @param LicenseRequest $request
     * @param Journey $journey
     * @return License
     */
    private function createLicense(LicenseRequest $request, Journey $journey): License
    {
        $train = TrainService::getTrainById(train_id: $journey->train->id);

        /**
         * ORIGIN
         */
        $origin_id = $request->validated('origin')['origin_id'];
        $origin_type = $request->validated('origin')['type'];
        $origin_status = StationSectionLoopStatuses::LICENSE_ISSUED->value;
        $origin = LicenseService::getModel(
            model_type: $origin_type,
            model_id: $origin_id,
        );

        /**
         * DESTINATION
         */
        $destination_id = $request->validated('destination')['destination_id'];
        $destination_type = $request->validated('destination')['type'];
        $destination_status = StationSectionLoopStatuses::LICENSE_ISSUED->value;
        $destination = LicenseService::getModel(
            model_type: $destination_type,
            model_id: $destination_id,
        );

        $throughs = $request->validated('through');
        $updated_throughs = array_map(
            callback: function (array $through) use ($throughs): array {
                // Set 'NEXT' for the first point in `through`, 'PENDING' for the rest
                $in_route = $through === reset($throughs)
                    ? LicenseRouteStatuses::NEXT->value
                    : LicenseRouteStatuses::PENDING->value;

                return [
                    'id' => $through['id'],
                    'type' => $through['type'],
                    'train_is_here' => false,
                    'status' => StationSectionLoopStatuses::LICENSE_ISSUED->value,
                    'start_time' => null,
                    'end_time' => null,
                    'in_route' => $in_route,
                ];
            },
            array: $throughs,
        );

        // Determine in_route for destination based on the presence of `through`
        $destination_in_route = empty($throughs)
            ? LicenseRouteStatuses::NEXT->value
            : LicenseRouteStatuses::PENDING->value;

        $uniqueLicenseNumber = $this->licenseService->getLicense();
        $license = $this->licenseService->acceptJourneyRequest(
            licenseData: [
                'license_number' => $uniqueLicenseNumber,
                'journey_id' => $journey->id,
                'direction' => JourneyService::getJourneyDirection(
                    origin: $train->origin->start_kilometer,
                    destination: $train->destination->start_kilometer,
                )->value,

                'origin' => [
                    'id' => $origin_id,
                    'type' => $origin_type,
                    'status' => $origin_status,
                    'name' => LicenseService::getLicenseOrigin(
                        model: $origin,
                    ),
                    'start_time' => null,  // Set start time for origin
                    'end_time' => null,
                    'in_route' => LicenseRouteStatuses::PENDING->value,
                ],
                'train_at_origin' => true,

                'through' => $updated_throughs,

                'destination' => [
                    'id' => $destination_id,
                    'type' => $destination_type,
                    'status' => $destination_status,
                    'name' => LicenseService::getLicenseOrigin(
                        model: $destination,
                    ),
                    'start_time' => null,
                    'end_time' => null,
                    'in_route' => $destination_in_route,
                ],
                'train_at_destination' => false,
            ],
        );

        $journey->train->driver->notify(new LicenseNotification(
            license: $license,
            type: NotificationTypes::REQUEST_ACCEPTED,
        ));

        return $license;
    }

}
