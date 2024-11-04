<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Carbon\Carbon;
use Domains\Driver\Enums\LicenseRouteStatuses;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Operator\Services\LicenseService;
use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Services\AtomikLogService;
use Domains\SuperAdmin\Models\Group;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Domains\SuperAdmin\Services\TrainService;
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

            if ( ! $group_with_train_origin) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'We cannot find a shift / group connected to your origin.',
                );
            }


            $currentTime = Carbon::now()->format('H:i');
            $shift = $group_with_train_origin->shifts->filter(function ($shift) use ($currentTime) {
                return
                    Carbon::now()->isSameDay(Carbon::parse($shift['day']))
                    && $currentTime >= $shift['from']
                    && $currentTime <= $shift['to'];
            })->first();

            if ( ! $shift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Please inform your system administrator there is no active shift currently set for the line you are trying to request a trip.',
                );
            }

            $journey = $this->journeyService->createJourney(
                journeyData: array_merge(
                    [
                        'shifts' => [$shift->id],
                        'direction' => $journey_direction->value,
                    ],
                    $request->validated(),
                ),
            );

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

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO1,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class($journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user->id,
                'current_location' => $journey->train->origin->name,
                'train_id' => $journey->train_id,
            ]));

            return $journey;
        });

        return response(
            content: [
                'message' => 'Journey created successfully.',
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
    public function createLocation(LocationRequest $request, Journey $journey): Response | HttpException
    {
        if ( ! $this->journeyService->createTrainLocation(
            journey: $journey,
            locationData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Location confirmation failed.',
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
                'current_location' => $license->origin['name'],
                'train_id' => $journey->train_id,
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
     * END JOURNEY
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function endJourney(Journey $journey): Response|HttpException
    {

        $ended =  DB::transaction(function () use ($journey): bool {
            if ( ! $journey->update(attributes: [
                'is_active' => false,
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

            defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
                'type' => AtomikLogsTypes::MACRO10,
                'resourceble_id' => $journey->id,
                'resourceble_type' => get_class(object: $journey),
                'actor_id' => Auth::id(),
                'receiver_id' => $shift->user_id,
                'current_location' => $journey->train->origin->name,
                'train_id' => $journey->train_id,
            ]));



            // $prev_latest_license = LicenseService::getPrevLatestLicense(
            //     journey: $journey,
            // );

            // if ($prev_latest_license) {
            //     if ( ! $prev_latest_license->train_at_destination) {
            //         abort(
            //             code: Http::EXPECTATION_FAILED(),
            //             message: 'You have not arrived at your current license destination yet',
            //         );
            //     }

            //     if (Station::class === $prev_latest_license->destination->type) {
            //         if ($prev_latest_license->destination->id === $journey->train->destination->id) {
            //             $licenses = $journey->licenses;
            //             if ($licenses && $licenses->count() > 0) {
            //                 foreach ($licenses as $license) {
            //                     $license->update([
            //                         'status' => LicenseStatuses::USED->value,
            //                     ]);
            //                 }
            //             }

            //             defer(callback: fn() => AtomikLogService::createAtomicLog(atomikLogData: [
            //                 'type' => AtomikLogsTypes::MACRO10,
            //                 'resourceble_id' => $journey->id,
            //                 'resourceble_type' => get_class(object: $journey),
            //                 'actor_id' => Auth::id(),
            //                 'receiver_id' => $shift->user_id,
            //                 'current_location' => $journey->train->origin->name,
            //                 'train_id' => $journey->train_id,
            //             ]));

            //             return true;
            //         }
            //     }
            // }

            // abort(
            //     code: Http::EXPECTATION_FAILED(),
            //     message: 'Your journey is still in progress',
            // );

            return true;
        });

        if ( ! $ended) {
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
}
