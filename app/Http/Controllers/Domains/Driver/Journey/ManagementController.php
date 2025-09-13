<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Carbon\Carbon;
use Domains\Driver\Enums\LicenseRouteStatuses;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Notifications\LicenseAreasRevoked;
use Domains\Driver\Notifications\LicenseRejectedNotification;
use Domains\Driver\Notifications\RequestLineExitNotification;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Requests\RevokeLicenseArea;
use Domains\Operator\Requests\RevokeLicenseAreaRequest;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\Message;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Group;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
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
        private JourneyService $journeyService,
    ) {}


    /**
     * CREATE JOURNEY
     * @param CreateOrEditJourneyRequest $request
     * @return Response|HttpException
     */
    public function create(CreateOrEditJourneyRequest $request): Response|HttpException
    {
        DB::transaction(function () use ($request): Journey {
            if ($this->journeyService->activeJourney()) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'You already have an active journey.Contact operator for further inquires',
                );
            }

            $train = TrainService::getTrainById(
                train_id: $request->validated(key: "train_id"),
            );

            if ($train->trainName->is_active) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Train is active in another journey. Please contact your operator.',
                );
            }

            $journey_direction = JourneyService::getJourneyDirection(
                origin: $train->origin->start_kilometer,
                destination: $train->destination->start_kilometer,
            );

            $group_with_train_origin = Group::query()
                ->whereJsonContains('stations', $train->origin->id)
                ->whereHas('shifts', function ($query): void {
                    $query->where('status', ShiftStatuses::CONFIRMED->value)
                        ->where('active', true);
                })
                ->with(['shifts' => function ($query): void {
                    $query->where('status', ShiftStatuses::CONFIRMED->value)
                        ->where('active', true)
                        ->with('user');
                }])
                ->first();


            if ( ! $group_with_train_origin) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'We cannot find a shift / group connected to your origin.',
                );
            }

            $currentTime = Carbon::now()->format('H:i');
            $shift = $group_with_train_origin->shifts->filter(fn($shift) =>
                    Carbon::now()->isSameDay(Carbon::parse($shift['day']))
                    && $currentTime >= $shift['from']
                    && $currentTime <= $shift['to']
                    && $shift->user->type->value === UserTypes::OPERATOR_CONTROLLER->value)->first();

            if ( ! $shift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Please inform your system administrator there is no active shift currently set for the line you are trying to request a trip.',
                );
            }

            $journey = null;
            $requesting_location_modal = JourneyService::getModel(
                model_type: $request->validated(key: "requesting_location.type"),
                model_id: $request->validated(key: "requesting_location.id"),
            );
            $requesting_location =  [
                'type' => $request->validated(key: "requesting_location.type"),
                'id' => $request->validated(key: "requesting_location.id"),
                'name' => JourneyService::getLocation(
                    model: $requesting_location_modal,
                ),
            ];

            if ($train->journey) {
                $logs = array_merge([
                    [
                        'type' => AtomikLogsTypes::DRIVER_REQUESTED_JOURNEY_RESTART->value,
                        'requested_by' => Auth::user()->employee_id,
                        'requested_at' => now(),
                        'requesting_location' => $requesting_location,
                    ],
                ], $train->journey->logs ?? []);

                $train->journey->update([
                    'is_active' => true,
                    'is_authorized' => false,
                    'requesting_location' => $requesting_location,
                    'logs' => $logs,
                    'has_obc' => true,
                ]);

                $journey = $train->journey;
            } else {
                $journey = $this->journeyService->createJourney(
                    journeyData: array_merge(
                        [
                            'shifts' => [$shift->id],
                            'direction' => $journey_direction->value,
                            'requesting_location' => $requesting_location,
                            'logs' => [[
                                'type' => AtomikLogsTypes::DRIVER_SENDS_MACRO1->value,
                                'send_by' => Auth::user()->employee_id,
                                'send_at' => now(),
                                'send_from' => $requesting_location,
                            ]],
                        ],
                        [
                            'train_id' => $request->validated('train_id'),
                            'has_obc' => true,
                        ],
                    ),
                );
            }

            if ( ! $journey) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Journey creation failed.',
                );
            }

            $shift->user->notify(new JourneyNotification(
                journey: $journey,
                type: NotificationTypes::JOURNEY_CREATED,
            ));

            JourneyService::updateTrainLength(
                train: $train,
                length: (int) $request->validated(key: "length"),
            );

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO1,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class($journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user->id,
                'current_location' => $requesting_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]);


            return $journey;
        });

        return response(
            content: [
                'message' => 'Line entry request send successfully.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * CONFIRM CURRENT TRAIN LOCATION
     * @param LocationRequest $request
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function createLocation(LocationRequest $request, Journey $journey, License $license): Response | HttpException
    {
        $updated = DB::transaction(function () use ($request, $journey, $license): bool {
            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(shift_id: end($shifts), );

            if ( ! $shift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Shift not found',
                );
            }

            if ($license->train_at_origin) {
                $origin = $license->origin;
                $origin['distance_remaining'] = $request->validated('distance_remaining');
                $license->origin = $origin;
            }

            if ($license->train_at_destination) {
                $destination = $license->destination;
                $destination['distance_remaining'] = $request->validated('distance_remaining');
                $license->destination = $destination;
            }

            $throughs = $license->through;
            if (is_array($throughs)) {
                foreach ($throughs as $index => $through) {
                    if ($through['train_is_here']) {
                        $through['distance_remaining'] = $request->validated('distance_remaining');
                        $throughs[$index] = $through;
                        break;
                    }
                }

                $license['through'] = $throughs;
            }

            $license->save();


            $model = LicenseService::getModel(
                model_type: $request->validated('type'),
                model_id: $request->validated('area_id'),
            );

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO6,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user_id,
                'current_location' => JourneyService::getLocation(model: $model) . ' - ' . $request->validated(key: 'location'),
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]);


            return true;
        });

        if ( ! $updated) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Location update failed. Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Location confirmation successful.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW JOURNEY
     * @param Journey $journey
     * @return Response
     */
    public function show(Journey $journey): Response
    {
        return response(
            content: [
                'message' => 'Journey fetched successful.',
                'journey' => new JourneyResource(
                    resource: $journey,
                ),
            ],
            status: Http::ACCEPTED(),
        );
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

            if ( ! $license->update([
                'status' => LicenseStatuses::CONFIRMED,
                'confirmed_at' => Carbon::now(),
                'origin' => $license_origin,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License confirmation failed. Please try again',
                );
            }

            $license->issuer->notify(new JourneyNotification(
                journey: $journey,
                type: NotificationTypes::LICENSE_CONFIRMED,
            ));

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO2,
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

            defer(callback: fn() => $license->update([
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
                'message' => 'License confirmed successfully. You can begin or continue with your journey.',
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * EXIT LINE REQUEST
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function sendExitLineRequest(Request $request, Journey $journey): Response|HttpException
    {
        $request_send = DB::transaction(function () use ($journey): bool {
            if ($journey->train->driver_id !== Auth::id()) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'An exit line request is only send by the driver.',
                );
            }

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            $shift->user->notify(new RequestLineExitNotification(journey: $journey));

            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::REQUEST_LINE_EXIT->value,
                    'requested_by' => Auth::user()->employee_id,
                    'requested_at' => now(),
                ],
            ], $journey->logs ?? []);

            $journey->update([
                'logs' => $logs,
            ]);

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::REQUEST_LINE_EXIT,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' =>  $shift->user->id,
                'current_location' => JourneyService::getTrainLocation(journey: $journey)['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]);

            return true;
        });

        if ( ! $request_send) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Line exit request failed.',
            );
        }


        return response(
            content: [
                'message' => 'Line exit request sent successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * EXIT LINE
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function exitLine(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        $exited =  DB::transaction(function () use ($journey, $notification): bool {
            $train_current_location = JourneyService::getTrainLocation(
                journey: $journey,
            );

            if ( ! $train_current_location) {
                $train_current_location = $journey->requesting_location;
            }

            if ( ! $journey->update([
                'is_active' => false,
                'last_destination' => $train_current_location,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Journey not ended.Please try again.',
                );
            }

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            $logs = array_merge([
                [
                    'type' => AtomikLogsTypes::MACRO10->value,
                    'line_exited_by' => Auth::user()->employee_id,
                    'line_exited_at' => now(),
                ],
            ], $journey->logs ?? []);

            $journey->update([
                'logs' => $logs,
            ]);

            if ( ! $journey->train->trainName->update([
                'is_active' => false,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'This train was not deactivated for this trip.Please try again.',
                );
            }

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
                $prev_latest_license->update([
                    'status' => LicenseStatuses::USED->value,
                    'logs' => $logs,
                ]);
            }

            $notification->markAsRead();

            $messages =  Message::query()
                ->where('sender_id', Auth::id())
                ->orWhere('receiver_id', Auth::id())
                ->where('is_active', true)
                ->pluck('id');

            Message::whereIn(column: 'id', values: $messages)->update([
                'is_active' => false,
            ]);

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO10,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user_id,
                'current_location' => $train_current_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
            ]);

            return true;
        });

        if ( ! $exited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Journey not ended.Please try again.',
            );
        }


        return response(
            content: [
                'message' => 'Journey ended successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * REJECT LICENSE
     * @param Request $request
     * @param Journey $journey
     * @param License $license
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function rejectLicense(Request $request, Journey $journey, License $license, DatabaseNotification $notification): Response|HttpException
    {
        $result = DB::transaction(function () use ($request, $journey, $license, $notification): bool {
            if (null !== $notification->read_at) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'License rejected already.Incase of inquires please contact your system administration.',
                );
            }

            $logs = array_merge([
                ['type' => AtomikLogsTypes::LICENSE_REJECTED->value,
                    'rejected_by' => Auth::user()->employee_id,
                    'rejected_at' => now(),
                    'reason_for_rejection' => $request->get(key: 'reason_for_rejection'),
                ],
            ], $license->logs ?? []);

            $license->update([
                'status' => LicenseStatuses::REJECTED->value,
                'logs' => $logs,
            ]);

            $prev_latest_license = License::query()
                ->where('journey_id', $license->journey_id)
                ->where('status', LicenseStatuses::USED->value)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($prev_latest_license) {
                $prev_latest_license->update([
                    'status' => LicenseStatuses::CONFIRMED->value,
                ]);
            }

            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts),
            );

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::LICENSE_REJECTED,
                'resourceble_id' => $license->id,
                'resourceble_type' => get_class(object: $license),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user_id,
                'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
                'train_id' => $journey->train_id,
                'locomotive_number_id' => $journey->train->locomotive_number_id,
                'message' => $request->get(key: 'reason_for_rejection'),
            ]));

            $license->issuer->notify(new LicenseRejectedNotification(
                journey: $journey,
                reason_for_rejection: $request->get(key: 'reason_for_rejection'),
            ));

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
                'message' => 'License rejected successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ACCEPT REVOKE LICENSE AREA REQUEST
     * @param RevokeLicenseAreaRequest $request
     * @param License $license
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function acceptRevokeLicenseAreaRequest(RevokeLicenseArea $request, License $license, DatabaseNotification $notification): Response|HttpException
    {
        $destination = $license->destination; // Get the destination array directly
        $through = collect($license->through); // Wrap the through array in a collection

        $area_id = (int) $request->validated('area_id');
        $area_type = $request->validated('type');

        $revoke_fields = function (&$area): void {
            $area['status'] = StationSectionLoopStatuses::GOOD->value;
            $area['in_route'] = LicenseRouteStatuses::REVOKED->value;
        };

        // Step 1: Check if license only has origin and destination
        if ($through->isEmpty()) {
            $revoke_fields($destination);
            $license->train_at_destination = false; // Update train status
            $license->destination = $destination; // Set the updated destination
            $license->save();

            return response(
                content: [
                    'message' => 'License section (s) have been revoked successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        // Step 2: Attempt to locate the revoke area in the `through` array
        $revoke_started = false;
        $affected_parts = [];
        $unaffected_parts = [];

        foreach ($through as &$area) {
            if ($area['id'] === $area_id && $area['type'] === $area_type) {
                // We found the area to revoke; mark as started
                $revoke_started = true;
            }

            // If we have started revocation, revoke this area
            if ($revoke_started) {
                $revoke_fields($area); // Revoke this area
                $affected_parts[] = $area; // Store affected area
            } else {
                // Otherwise, store unaffected areas
                $unaffected_parts[] = $area;
            }
        }

        // Step 3: Revoke the destination if revocation started or if it matches
        if ($revoke_started || ($destination['id'] === $area_id && $destination['type'] === $area_type)) {
            $revoke_fields($destination);
            $license->train_at_destination = false; // Update train status
        }

        // Step 4: Build the new thought array without the destination
        $new_thought = array_merge($unaffected_parts, $affected_parts);

        // Update the license
        $license->destination = $destination; // Set updated destination
        $license->through = $new_thought; // Set updated through array without destination
        $license->save();

        $license->issuer->notify(new LicenseAreasRevoked(
            message: 'License section area (s) revoke request accepted.',
            description: 'Your request to revoke some areas in the active license of train ' . $license->journey->train->locomotiveNumber->number . ' has been accepted by the driver.',
            area_id: $request->validated(key: 'area_id'),
            type: $request->validated(key: 'type'),
            notificationTypes: NotificationTypes::LICENSE_AREAS_REVOKED,
            license_id: $license->id,
        ));

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LICENSE_REVOKED->value,
                'revoked_by' => Auth::user()->employee_id,
                'revoked_at' => now(),
            ],
        ], $license->logs);

        $license->update([
            'logs' => $logs,
        ]);

        defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LICENSE_REVOKED,
            'resourceble_id' => $license->id,
            'resourceble_type' => get_class(object: $license),
            'actor_id' => Auth::id(),
            'receiver_id' => $license->issuer_id,
            'current_location' => JourneyService::getTrainLocation(journey: $license->journey)['name'],
            'train_id' => $license->journey->train_id,
            'locomotive_number_id' => $license->journey->train->locomotive_number_id,
        ]));

        $notification->markAsRead();

        return response(
            content: [
                'message' => 'License section (s) have been revoked successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * LICENSE SECTION AREAS REVOKE REJECT
     * @param Request $request
     * @param License $license
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function rejectRevokeLicenseAreaRequest(Request $request, License $license, DatabaseNotification $notification): Response|HttpException
    {
        $license->issuer->notify(new LicenseAreasRevoked(
            message: 'License section area (s) revoke request rejected.',
            description: 'Your request to revoke some areas in the active license of train ' . $license->journey->train->locomotiveNumber->number . ' has been rejected by the driver with reason [ ' . $request->get('reason_for_rejection') . ' ]',
            area_id: $request->get(key: 'area_id'),
            type: $request->get(key: 'type'),
            notificationTypes: NotificationTypes::LICENSE_REVOKE_REJECTED,
            license_id: $license->id,
        ));

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LICENSE_REVOKE_REJECTED->value,
                'request_rejected_by' => Auth::user()->employee_id,
                'request_rejected_at' => now(),
                'reason_for_rejection' => $request->get('reason_for_rejection'),
            ],
        ], $license->logs);

        $license->update([
            'logs' => $logs,
        ]);

        defer(callback: fn() =>  AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LICENSE_REVOKE_REJECTED,
            'resourceble_id' => $license->id,
            'resourceble_type' => get_class(object: $license),
            'actor_id' => Auth::id(),
            'receiver_id' => $license->issuer_id,
            'current_location' => JourneyService::getTrainLocation(journey: $license->journey)['name'],
            'train_id' => $license->journey->train_id,
            'locomotive_number_id' => $license->journey->train->locomotive_number_id,
            'message' => $request->get(key: 'reason_for_rejection'),
        ]));


        $notification->markAsRead();

        return response(
            content: [
                'message' => 'Request to revoke license rejected successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
