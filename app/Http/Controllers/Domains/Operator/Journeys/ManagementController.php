<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Journeys;

use Carbon\Carbon;
use Domains\Driver\Models\Journey;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Notifications\AcceptLineExitRequestNotification;
use Domains\Operator\Notifications\RejectLineExitRequest;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Models\Group;
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
        protected JourneyService $journeyService,
    ) {}

    /**
     * REJECT EXIT LINE REQUEST
     * @param Request $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function rejectExitLineRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $journey->train->driver->notify(new RejectLineExitRequest(
            journey: $journey,
            reason_for_rejection: $request->get(key: 'reason_for_rejection'),
        ));

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_REJECTED->value,
                'rejected_by' => Auth::user()->employee_id,
                'rejected_at' => now(),
                'reason_for_rejection' => $request->get(key: 'reason_for_rejection'),
            ],
        ], $journey->logs ?? []);

        $journey->update(attributes: [
            'logs' => $logs,
        ]);

        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_REJECTED,
            'resourceble_id' => $journey->id,
            'resourceble_type' => get_class(object: $journey),
            'actor_id' => Auth::id(),
            'receiver_id' => $journey->train->driver_id,
            'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
            'train_id' => $journey->train_id,
            'locomotive_number_id' => $journey->train->locomotive_number_id,
            'message' => $request->get(key: 'reason_for_rejection'),
        ]);


        $notification->markAsRead();

        return response(
            content: [
                'message' => 'Exit line request rejected successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * ACCEPT EXIT LINE REQUEST
     * @param Request $request
     * @param Journey $journey
     * @param DatabaseNotification $notification
     * @return Response|HttpException
     */
    public function acceptExitLineRequest(Request $request, Journey $journey, DatabaseNotification $notification): Response|HttpException
    {
        if (null !== $notification->read_at) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This request was processed before.Please contact your system administration for more inquires on the same.',
            );
        }

        $journey->train->driver->notify(new AcceptLineExitRequestNotification(journey: $journey));
        $notification->markAsRead();

        $logs = array_merge([
            [
                'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_ACCEPTED->value,
                'accepted_by' => Auth::user()->employee_id,
                'accepted_at' => now(),
            ],
        ], $journey->logs ?? []);

        $journey->update(attributes: [
            'logs' => $logs,
        ]);

        AtomikLogService::createAtomicLog(atomikLogData: [
            'type' => AtomikLogsTypes::LINE_EXIT_REQUEST_ACCEPTED,
            'resourceble_id' => $journey->id,
            'resourceble_type' => get_class(object: $journey),
            'actor_id' => Auth::id(),
            'receiver_id' => $journey->train->driver_id,
            'current_location' => JourneyService::getTrainLocation(journey: $journey) ? JourneyService::getTrainLocation(journey: $journey)['name'] : $journey->requesting_location['name'],
            'train_id' => $journey->train_id,
            'locomotive_number_id' => $journey->train->locomotive_number_id,
        ]);

        return response(
            content: [
                'message' => 'Exit line request accepted successfully.',
            ],
            status: Http::ACCEPTED(),
        );

    }

    /**
     * REQUEST LINE ENTRY
     * @param CreateOrEditJourneyRequest $request
     * @return Response
     */
    public function requestLineEntry(CreateOrEditJourneyRequest $request): Response
    {
        DB::transaction(function () use ($request): Journey {
            $train = TrainService::getTrainById(
                train_id: $request->validated(key: "train_id"),
            );

            $journey_direction = JourneyService::getJourneyDirection(
                origin: $train->origin->start_kilometer,
                destination: $train->destination->start_kilometer,
            );

            $group_with_train_origin = Group::query()
                ->with(relations: ['shifts' => function ($query): void {
                    $query->where('status', ShiftStatuses::CONFIRMED->value)
                        ->where('active', true)
                        ->with('user');
                }])
                ->whereJsonContains(column: 'stations', value: $train->origin->id)
                ->first();

            $currentTime = Carbon::now()->format('H:i');
            $shift = $group_with_train_origin->shifts->filter(fn($shift) =>
                    Carbon::now()->isSameDay(Carbon::parse($shift['day']))
                    && $currentTime >= $shift['from']
                    && $currentTime <= $shift['to'])->first();

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
                        'type' => AtomikLogsTypes::OPERATOR_REQUESTED_JOURNEY_RESTART->value,
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
                    'has_obc' => false,
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
                                'type' => AtomikLogsTypes::OPERATOR_SENDS_MACRO1->value,
                                'send_by' => Auth::user()->employee_id,
                                'send_at' => now(),
                                'send_from' => $requesting_location,
                            ]],
                        ],
                        [
                            'train_id' => $request->validated(key: 'train_id'),
                            'has_obc' => false,
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

            AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::OPERATOR_MACRO1,
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
}
