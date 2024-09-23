<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Carbon\Carbon;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Notifications\LicenseRequestNotification;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Models\Shift;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
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

            // $currentShift = Shift::where('shift_start_station_id', '<=', $journey->origin_station_id)
            //     ->where('shift_end_station_id', '>=', $journey->origin_station_id)
            //     ->with('user')
            //     ->first();


            $shift = Shift::query()
                ->where("line_id", $request->validated(key: "line_id"))
                ->where("status", ShiftStatuses::PENDING)
                ->whereJsonContains('stations', $request->validated('origin_station_id'))->first();

            dd($shift);


            // if ( ! $currentShift) {
            //     abort(
            //         code: Http::EXPECTATION_FAILED(),
            //         message: 'Please inform your system administration that you have no operator set for the desk you are trying to request a trip.',
            //     );
            // }

            // $currentShift->user->notify(new JourneyNotification(
            //     journey: $journey,
            //     type: NotificationTypes::JOURNEY_CREATED,
            // ));

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

    public function requestLicense(Journey $journey)
    {
        // Get the last license issued for this journey
        $lastLicense = License::where('journey_id', $journey->id)
            ->orderBy('issued_at', 'desc')
            ->first();

        // Get the shift responsible for the last destination station
        $currentShift = Shift::where('shift_start_station_id', '<=', $lastLicense->destination_station_id)
            ->where('shift_end_station_id', '>=', $lastLicense->destination_station_id)
            ->first();

        // If journey is still within the current shift
        if ($lastLicense->destination_station_id < $currentShift->shift_end_station_id) {
            // Notify the current shift to assign the next license
            $currentShift->user->notify(new LicenseRequestNotification(
                journey: $journey,
                lastLicense: $lastLicense,
                type: NotificationTypes::LICENSE_REQUEST,
            ));

            return response(
                content: [
                    'message' => 'Your request for a license has been send successfully to your current operator. You will be notified when the new license is assigned for your confirmation.',
                ],
                status: Http::ACCEPTED(),
            );
        }

        $nextShift = Shift::where('shift_start_station_id', '>', $lastLicense->destination_station_id)
            ->first();

        if ( ! $nextShift) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Please inform your system administration that you have no operator set for the desk you are trying to request a trip.',
            );
        }

        $nextShift->user->notify(new LicenseRequestNotification(
            journey: $journey,
            lastLicense: $lastLicense,
            type: NotificationTypes::LICENSE_REQUEST,
        ));

        return response(
            content: [
                'message' => 'Your request for a license has been send successfully to your next operator. You will be notified when the new license is assigned for your confirmation.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
