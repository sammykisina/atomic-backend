<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Licenses;

use Carbon\Carbon;
use Domains\Driver\Enums\LicenseRouteStatuses;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\LicenseAreasRevoked;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\LicenseTypes;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Notifications\DeclineLineEntryRequestNotification;
use Domains\Operator\Notifications\LicenseNotification;
use Domains\Operator\Requests\LicenseRequest;
use Domains\Operator\Requests\RevokeLicenseAreaRequest;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\AtomikLog;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Database\Eloquent\Model;
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
            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::OPERATOR_AUTHORIZES_LINE_ENTRY_REQUEST->value,
                    'authorized_by' => Auth::user()->employee_id,
                    'authorized_at' => now(),
                ],
            ], $journey->logs ?? []);

            $is_updated = $journey->update(attributes: [
                'is_authorized' => true,
                'logs' => $logs,
            ]);

            if ( ! $journey->train->trainName->update([
                'is_active' => true,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This train was not activated for this trip.Please try again.',
                );
            }

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO3,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $journey->train->driver_id,
                'current_location' => $journey->requesting_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
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
            if ($request->validated('type') === LicenseTypes::SOS->value) {
                if (Auth::user()->type !== UserTypes::OPERATOR_CONTROLLER_SUPERVISOR->value) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'You are not allowed to assign an SOS license. Please notify your supervisor.',
                    );
                }
            }

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

            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::LICENSE_USED->value,
                    'marked_as_used_by' => Auth::user()->employee_id,
                    'marked_at' => now(),
                ],
            ], $prev_latest_license->logs ?? []);

            if ($prev_latest_license) {
                $prev_latest_license->update(attributes: [
                    'status' => LicenseStatuses::USED->value,
                    'logs' => $logs,
                ]);
            }

            $license = $this->createLicense(
                request: $request,
                journey: $journey,
            );

            $journey->update(attributes: [
                'shifts' => $currentShiftIds,
            ]);

            defer(callback: fn() => LicenseService::createLicenseAfterMaths(
                license: $license,
                journey: $journey,
            ));


            if ($journey->has_obc) {
                $journey->train->driver->notify(new LicenseNotification(
                    license: $license,
                    type: NotificationTypes::REQUEST_ACCEPTED,
                ));
            } else {
                $shift->user->notify(
                    new LicenseNotification(
                        license: $license,
                        type: NotificationTypes::REQUEST_ACCEPTED,
                    ),
                );
            }

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
     * ASSIGN SOS LICENSE
     * @param LicenseRequest $request
     * @param Journey $journey
     * @return Response
     */
    public function sosLicense(LicenseRequest $request, Journey $journey): Response
    {
        DB::transaction(function () use ($request, $journey): License {
            if ($request->validated('type') === LicenseTypes::SOS->value) {
                if (Auth::user()->type !== UserTypes::OPERATOR_CONTROLLER_SUPERVISOR->value) {
                    abort(
                        code: Http::EXPECTATION_FAILED(),
                        message: 'You are not allowed to assign an SOS license. Please notify your supervisor.',
                    );
                }
            }

            $shift  = Shift::query()
                ->where('user_id', Auth::id())
                ->where('status', ShiftStatuses::CONFIRMED->value)
                ->where('active', operator: true)
                ->first();

            $currentShiftIds = $journey->shifts ?? [];
            $currentShiftIds[] = $shift->id;

            $prev_latest_license = LicenseService::getPrevLatestLicense(
                journey: $journey,
            );

            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::LICENSE_USED->value,
                    'marked_as_used_by' => Auth::user()->employee_id,
                    'marked_at' => now(),
                ],
            ], $prev_latest_license->logs ?? []);

            if ($prev_latest_license) {
                $prev_latest_license->update(attributes: [
                    'status' => LicenseStatuses::USED->value,
                    'logs' => $logs,
                ]);
            }

            $license = $this->createLicense(
                request: $request,
                journey: $journey,
            );

            $journey->update(attributes: [
                'shifts' => $currentShiftIds,
            ]);

            $license->update(attributes: [
                'logs' => [
                    [
                        'type' => AtomikLogsTypes::SOS_LICENSE_CREATED->value,
                        'created_at' => $license->created_at,
                        'created_by' => Auth::user()->employee_id,
                    ],
                ],
            ]);

            if ($license->type === LicenseTypes::SOS->value) {
                AtomikLogService::createAtomicLog(atomikLogData: [
                    'type' => AtomikLogsTypes::SOS_LICENSE_CREATED,
                    'resourceble_id' => $license->id,
                    'resourceble_type' => get_class(object: $license),
                    'actor_id' => Auth::id(),
                    'receiver_id' => $journey->train->driver_id,
                    'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
                    'train_id' => $journey->train_id,
                    'locomotive_number_id' => $journey->train->locomotive_number_id,
                    'message' => $request->validated(key: 'reason_for_sos_license'),
                ]);
            }

            /**
             * UPDATE THE JOURNEY TO BE RESCUED
             */
            $journey_to_be_rescued = JourneyService::getJourneyById(journey_id: $request->validated(key: 'journey_to_be_rescued'));
            $this->markTrainAsRescued(journey: $journey_to_be_rescued);

            if ($journey->has_obc) {
                $journey->train->driver->notify(new LicenseNotification(
                    license: $license,
                    type: NotificationTypes::REQUEST_ACCEPTED,
                ));
            } else {
                $shift->user->notify(
                    new LicenseNotification(
                        license: $license,
                        type: NotificationTypes::REQUEST_ACCEPTED,
                    ),
                );
            }

            return $license;
        });

        return response(
            content: [
                'message' => 'SOS License assigned successfully. Awaiting confirmation.',
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

        $is_declined =  DB::transaction(function () use ($request, $journey, $notification): bool {
            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::DECLINE_LINE_ENTRY_REQUEST->value,
                    'declined_by' => Auth::user()->employee_id,
                    'declined_at' => now(),
                ],
            ], $journey->logs ?? []);


            $journey->update(attributes: [
                'logs' => $logs,
                'is_active' => false,
            ]);

            $journey->train->driver->notify(new DeclineLineEntryRequestNotification(
                journey: $journey,
                reason_for_decline: $request->get(key: 'reason_for_decline'),
            ));

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::DECLINE_LINE_ENTRY_REQUEST,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $journey->train->driver_id,
                'current_location' => $journey->requesting_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
                'message' => $request->get(key: 'reason_for_decline'),
            ]);

            $notification->markAsRead();

            $is_declined = $journey->delete();

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
                'message' => 'Request for line entry has been declined successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * REVOKE LICENSE AREA (S)
     * @param RevokeLicenseAreaRequest $request
     * @param License $license
     * @return Response|HttpException
     */
    public function revokeLicenseArea(RevokeLicenseAreaRequest $request, License $license): Response|HttpException
    {
        $license->journey->train->driver->notify(new LicenseAreasRevoked(
            message: 'License section area(s) revocation initiated',
            description: 'Please be advised that some sections in your active license have been marked to be revoked (canceled). with reason [ ' . $request->validated(key: 'reason_for_revoking') . ' ]',
            area_id: $request->validated('area_id'),
            type: $request->validated('type'),
            notificationTypes: NotificationTypes::LICENSE_REVOKE_REQUEST,
            license_id : $license->id,
        ));

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LICENSE_REVOKE_REQUEST->value,
                'send_by' => Auth::user()->employee_id,
                'send_at' => now(),
            ],
        ], $license->logs);

        $license->update(attributes: [
            'logs' => $logs,
        ]);

        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LICENSE_REVOKE_REQUEST,
            'resourceble_id' => $license->id,
            'resourceble_type' => get_class(object: $license),
            'actor_id' => Auth::id(),
            'receiver_id' => $license->journey->train->driver_id,
            'current_location' => JourneyService::getTrainLocation(journey: $license->journey)['name'],
            'train_id' => $license->journey->train_id,
            'locomotive_number_id' => $license->journey->train->locomotive_number_id,
            'message' => $request->validated(key: 'reason_for_revoking'),
        ]);


        return response(
            content: [
                'message' => 'Driver of train ' . $license->journey->train->locomotiveNumber->number . " has been notified of your intention to revoke some of his/her license section(s).Please await response.",
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * MARK TRAIN AS RESCUED
     * @param Journey $journey
     * @return void
     */
    private function markTrainAsRescued(Journey $journey): void
    {
        $latest_train_license = License::query()
            ->where('journey_id', $journey->id)
            ->where('status', LicenseStatuses::CONFIRMED->value)
            ->first();

        if ( ! $latest_train_license) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The train to be rescued was not assigned any licenses yet.Please assign it a license so that its status can be tracked',
            );
        }

        // Update origin if train is at origin
        if ($latest_train_license['train_at_origin']) {
            $origin = $latest_train_license->origin;
            $origin['in_route'] = LicenseRouteStatuses::RESCUED->value;
            $latest_train_license->origin = $origin;
        }

        // Update the 'throughs' array, modifying the specific 'through' element where the train is present
        $throughs = $latest_train_license['through'];
        if (is_array($throughs)) {
            foreach ($throughs as $index => $through) {
                if ($through['train_is_here']) {
                    $through['in_route'] = LicenseRouteStatuses::RESCUED->value;
                    $throughs[$index] = $through; // Update the specific 'through' in the array
                    break; // Assuming only one 'train_is_here' exists
                }
            }
            $latest_train_license['through'] = $throughs;
        }

        // Update destination if train is at destination
        if ($latest_train_license['train_at_destination']) {
            $destination = $latest_train_license->destination;
            $destination['in_route'] = LicenseRouteStatuses::RESCUED->value;
            $latest_train_license->destination = $destination;
        }

        $latest_train_license->save();
    }


    /**
     * CREATE LICENSE
     * @param LicenseRequest $request
     * @param Journey $journey
     * @return License
     */
    private function createLicense(LicenseRequest $request, Journey $journey): License
    {
        /**
         * ORIGIN
         */
        $origin_id = $request->validated('origin')['origin_id'];
        $origin_type = $request->validated('origin')['type'];
        $origin_coordinates = $request->validated('origin')['coordinates'];
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
        $destination_coordinates = $request->validated('destination')['coordinates'];
        $destination_status = StationSectionLoopStatuses::LICENSE_ISSUED->value;
        $destination = LicenseService::getModel(
            model_type: $destination_type,
            model_id: $destination_id,
        );

        /**
         * CHECK LICENSE JOURNEY DIRECTION
         */
        $journey_direction = $journey->direction;

        $license_direction = JourneyService::getJourneyDirection(
            origin: $this->getKMs(
                type: AtomikLog::getResourcebleType(namespaceString: get_class(object: $origin)),
                model: $origin,
            ),
            destination: $this->getKMs(
                type: AtomikLog::getResourcebleType(namespaceString: get_class(object: $destination)),
                model: $destination,
            ),
        )->value;

        if ($journey_direction->value !== $license_direction) {
            $latest_license = License::where('journey_id', $journey->id)
                ->latest()
                ->first();

            if ($latest_license) {
                foreach ($latest_license->through as $throughPoint) {
                    if ($throughPoint['in_route'] === LicenseRouteStatuses::NEXT->value) {
                        abort(
                            code: Http::NOT_ACCEPTABLE(),
                            message: 'Your have an active area in the current license moving in the opposite direction.Please revoked it to create the new license.',
                        );
                    }
                }

                if ($latest_license->destination['in_route'] === LicenseRouteStatuses::NEXT->value) {
                    abort(
                        code: Http::NOT_ACCEPTABLE(),
                        message: 'Your have an active area in the current license moving in the opposite direction.Please revoked it to create the new license.',
                    );
                }
            }
        }


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
                    'coordinates' => $through['coordinates'],
                ];
            },
            array: $throughs,
        );

        // Determine in_route for destination based on the presence of `through`
        $destination_in_route = empty($throughs)
            ? LicenseRouteStatuses::NEXT->value
            : LicenseRouteStatuses::PENDING->value;

        $uniqueLicenseNumber = $this->licenseService->getLicense();
        $license = $this->licenseService->createJourneyLicense(
            licenseData: [
                'license_number' => $uniqueLicenseNumber,
                'journey_id' => $journey->id,
                'direction' => $license_direction,
                'type' => $request->validated('type') ?? LicenseTypes::SIMPLE->value,
                'reason_for_sos_license' => $request->validated('reason_for_sos_license') ?? null,

                'origin' => [
                    'id' => $origin_id,
                    'type' => $origin_type,
                    'coordinates' => $origin_coordinates,
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
                    'coordinates' => $destination_coordinates,
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

        return $license;
    }


    /**
     * Summary of getArea
     * @param string $type
     * @param Model $model
     * @return float
     */
    private function getKMs(string $type, Model $model): float
    {
        return match ($type) {
            'Station' => $model->start_kilometer,
            'Section' => $model->station->end_kilometer,
            'Loop' => $model->station->start_kilometer,
        };
    }


    /**
     * CONFIRM LICENSE
     * @param Journey $journey
     * @param License $license
     * @return Response|HttpException
     */
    public function confirmLicense(Journey $journey, License $license, DatabaseNotification $notification): Response | HttpException
    {
        $result = DB::transaction(function () use ($journey, $license, $notification): bool {
            if (null !== $notification->read_at) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License confirmation already.Incase of inquires please contact your system administration.',
                );
            }

            $license_origin = $license->origin;
            $license_origin['in_route'] = LicenseRouteStatuses::OCCUPIED->value;
            $license_origin['start_time'] = Carbon::now();

            if ( ! $license->update(attributes: [
                'status' => LicenseStatuses::CONFIRMED,
                'confirmed_at' => Carbon::now(),
                'origin' => $license_origin,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License confirmation failed. Please try again',
                );
            }

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::OPERATOR_MACRO2,
                'resourceble_id' => $license->id,
                'resourceble_type' => get_class(object: $license),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user_id,
                'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]));


            $logs = array_merge([
                ['type' => AtomikLogsTypes::LICENSE_CONFIRMED->value,
                    'confirmed_by' => Auth::user()->employee_id,
                    'confirmed_at' => now(),
                ],
            ], $license->logs);

            defer(callback: fn() => $license->update(attributes: [
                'logs' => $logs,
            ]));

            $notification->markAsRead();

            return true;
        });


        if ( ! $result) {
            return response(
                content: [
                    'message' => 'Something went wrong.Please try again.',
                ],
                status: Http::NOT_IMPLEMENTED(),
            );
        }

        return response(
            content: [
                'message' => 'License confirmed successfully. Please inform the driver to begin or continue with his/her journey.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
