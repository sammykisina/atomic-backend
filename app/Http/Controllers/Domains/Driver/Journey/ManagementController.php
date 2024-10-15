<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Carbon\Carbon;
use Domains\Driver\Enums\AreaTypes;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Requests\ClearRequest;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Models\Station;
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
    public function create(CreateOrEditJourneyRequest $request): Response | HttpException
    {
        $journey = DB::transaction(function () use ($request): Journey {
            if ($this->journeyService->activeJourney()) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'You already have an active journey. Request license on it instead or contact operator for further inquires',
                );
            }

            $shift = Shift::query()
                ->where("line_id", $request->validated(key: "line_id"))
                ->where("status", ShiftStatuses::CONFIRMED)
                ->where("active", true)
                ->whereJsonContains('stations', $request->validated('origin_station_id'))
                ->first();

            if ( ! $shift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Please inform your system administrator there is no active shift currently set for the line you are trying to request a trip.',
                );
            }

            $journey = $this->journeyService->createJourney(
                journeyData: $request->validated(),
            );

            if ( ! $journey) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Journey creation failed.',
                );
            }

            $this->journeyService->createTrainLocation(
                journey: $journey,
                locationData: [
                    'station_id' => $journey->origin->id,
                    'latitude' => $request->validated(key: "current_location_latitude"),
                    'longitude' => $request->validated(key: "current_location_longitude"),
                ],
            );

            $shift->user->notify(new JourneyNotification(
                journey: $journey,
                type: NotificationTypes::JOURNEY_CREATED,
            ));

            return $journey;
        });

        // broadcast(
        //     event: new NotificationSent(
        //         receiver: $operator,
        //         sender: $journey->driver,
        //         message: "A new journey ".$journey->origin->name . " to " . $journey->destination->name  ." has been created. Please check it out.",
        //     ),
        // );

        return response(
            content: [
                'journey' => new JourneyResource(
                    resource: $journey,
                ),
                'message' => 'Journey created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT JOURNEY
     * @param CreateOrEditJourneyRequest $request
     * @param Journey $journey
     * @return Response | HttpException
     */
    // public function edit(CreateOrEditJourneyRequest $request, Journey $journey): Response | HttpException
    // {
    //     $edited = DB::transaction(function () use ($request, $journey): bool {
    //         $edited = $this->journeyService->editJourney(
    //             journey: $journey,
    //             updatedJourneyData: $request->validated(),
    //         );

    //         $this->journeyService->createTrainLocation(
    //             journey: $journey,
    //             locationData: [
    //                 'station_id' => $journey->origin->id,
    //                 'latitude' => $request->validated(key: "current_location_latitude"),
    //                 'longitude' => $request->validated(key: "current_location_longitude"),
    //             ],
    //         );

    //         $shift = ShiftService::firstJourneyAuthorizer(journey: $journey);

    //         if ( ! $shift) {
    //             abort(
    //                 code: Http::EXPECTATION_FAILED(),
    //                 message: 'Please inform your system administration that you have no operator set for the desk you are trying to request a trip.',
    //             );
    //         }

    //         $shift->user->notify(new JourneyNotification(
    //             journey: $journey,
    //             type: NotificationTypes::JOURNEY_EDITED,
    //         ));

    //         return $edited;
    //     });

    //     if ( ! $edited) {
    //         abort(
    //             code: Http::EXPECTATION_FAILED(),
    //             message: 'Journey creation failed.',
    //         );
    //     }

    //     return response(
    //         content: [
    //             'message' => 'Journey updated successfully.',
    //         ],
    //         status: Http::ACCEPTED(),
    //     );
    // }

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

            if ( ! $license->update([
                'status' => LicenseStatuses::CONFIRMED,
                'confirmed_at' => Carbon::now(),
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
     * REQUEST LICENSE
     * @param Journey $journey
     * @return void
     */
    public function requestLicense(Journey $journey): Response | HttpException
    {
        // ensure that the current license destination is not equal to the journey destination
        // $latest_journey_license = $journey->licenses()->latest()->first();
        // if ($latest_journey_license->destination_station_id === $journey->destination_station_id) {
        //     abort(
        //         code: Http::EXPECTATION_FAILED(),
        //         message: 'You are at your destination according to the journey information.If you wish to continue please end this journey and create a new one.',
        //     );
        // }

        // // check if the next license should be handled by the previous license issuer
        // $last_license_shift = $latest_journey_license->issuer->shifts()->latest()->first();

        // $journey_direction = JourneyService::getJourneyDirection(
        //     origin: $journey->origin->start_kilometer,
        //     destination: $journey->destination->end_kilometer,
        // );

        // // up train
        // if (LicenseDirections::UP_TRAIN === $journey_direction) {
        //     if ($latest_journey_license->destination_station_id === max($last_license_shift->stations)) {
        //         // look for the next station
        //         $next_shift = Shift::query()
        //             ->where("status", ShiftStatuses::CONFIRMED)
        //             ->where("active", operator: true)
        //             ->whereJsonContains("stations", max($last_license_shift->stations) + 1)
        //             ->first();

        //         if ( ! $next_shift) {
        //             abort(
        //                 code: Http::EXPECTATION_FAILED(),
        //                 message: 'Opps! You don`t have an active shift to accept this request. Please contact your system administrator.',
        //             );
        //         }



        //         $next_shift->user->notify(new LicenseRequestNotification(
        //             journey: $journey,
        //             lastLicense: $latest_journey_license,
        //             type: NotificationTypes::LICENSE_REQUEST,
        //         ));
        //     } else {
        //         $last_license_shift->user->notify(new LicenseRequestNotification(
        //             journey: $journey,
        //             lastLicense: $latest_journey_license,
        //             type: NotificationTypes::LICENSE_REQUEST,
        //         ));
        //     }

        //     $latest_journey_license->update([
        //         'status' => LicenseStatuses::USED,
        //     ]);
        // }

        // // down train
        // if (LicenseDirections::DOWN_TRAIN === $journey_direction) {
        //     if ($latest_journey_license->destination_station_id === min($last_license_shift->stations)) {
        //         // look for the next station
        //         $next_shift = Shift::query()
        //             ->where("status", ShiftStatuses::CONFIRMED)
        //             ->where("active", operator: true)
        //             ->whereJsonContains("stations", min($last_license_shift->stations) - 1)
        //             ->first();

        //         if ( ! $next_shift) {
        //             abort(
        //                 code: Http::EXPECTATION_FAILED(),
        //                 message: 'Opps! You don`t have an active shift to accept this request. Please contact your system administrator.',
        //             );
        //         }

        //         $next_shift->user->notify(new LicenseRequestNotification(
        //             journey: $journey,
        //             lastLicense: $latest_journey_license,
        //             type: NotificationTypes::LICENSE_REQUEST,
        //         ));
        //     } else {
        //         $last_license_shift->user->notify(new LicenseRequestNotification(
        //             journey: $journey,
        //             lastLicense: $latest_journey_license,
        //             type: NotificationTypes::LICENSE_REQUEST,
        //         ));
        //     }

        //     $latest_journey_license->update([
        //         'status' => LicenseStatuses::USED,
        //     ]);
        // }

        return response(
            content: [
                // 'message' => 'Your license request has been sent successfully.Please be patient while your license is being assigned.',
                'message' => 'Feature under adjustment due to change of trip structure.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * END JOURNEY
     * @param Journey $journey
     * @return Response|HttpException
     */
    public function endJourney(Journey $journey): Response | HttpException
    {
        if ($journey->driver_id !== Auth::id()) {
            abort(
                code: Http::UNAUTHORIZED(),
                message: 'This journey can only be ended by the driver.',
            );
        }

        $latest_journey_license = $journey->licenses()->latest()->first();

        DB::transaction(function () use ($journey, $latest_journey_license): void {
            if ( ! $journey->update([
                'status' => false,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Journey not ended.Please try again.',
                );
            }

            if ($latest_journey_license) {
                $latest_journey_license->update([
                    'status' => LicenseStatuses::USED,
                ]);
            }

        });

        return response(
            content: [
                'message' => 'Journey ended successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * CLEAR LOOP STATION OR SECTION
     * @param ClearRequest $request
     * @return Response|HttpException
     */
    public function clear(ClearRequest $request): Response | HttpException
    {
        $loop = null;
        $station = null;
        $section = null;

        if ($request->validated('type') === AreaTypes::LOOP->value) {
            $loop = Loop::query()
                ->where('id', $request->validated('area_id'))
                ->first();

            if ( ! $loop) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Loop not found.',
                );
            }

            if ( ! $loop->update([
                'status' => StationSectionLoopStatuses::GOOD->value,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Loop not cleared. Please try again.',
                );
            }

            return response(
                content: [
                    'message' => 'Loop cleared successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }


        if ($request->validated('type') === AreaTypes::STATION->value) {
            $station = Station::query()
                ->where('id', $request->validated('area_id'))
                ->first();

            if ( ! $station) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Station not found.',
                );
            }

            if ( ! $station->update([
                'status' => StationSectionLoopStatuses::GOOD->value,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Station not cleared. Please try again.',
                );
            }

            return response(
                content: [
                    'message' => 'Station cleared successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }


        if ($request->validated('type') === AreaTypes::SECTION->value) {
            $section = Section::query()
                ->where('id', $request->validated('area_id'))
                ->first();

            if ( ! $section) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Section not found.',
                );
            }

            if ( ! $section->update([
                'status' => StationSectionLoopStatuses::GOOD->value,
            ])) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Section not cleared. Please try again.',
                );
            }

            return response(
                content: [
                    'message' => 'Section cleared successfully.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        return response(
            content: [
                'message' => 'Please provide acceptable area type.',
            ],
            status: Http::NOT_IMPLEMENTED(),
        );
    }
}
