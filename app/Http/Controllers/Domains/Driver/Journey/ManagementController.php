<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Enums\LicenseStatuses;
use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Driver\Notifications\JourneyNotification;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Services\Staff\EmployeeService;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpFoundation\Response;
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

            $operator = EmployeeService::currentDeskOperator(journey: $journey);

            if ( ! $operator) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Please inform your system administration that you have no operator set for the desk you are trying to request a trip.',
                );
            }

            $operator->notify(new JourneyNotification(
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
    public function edit(CreateOrEditJourneyRequest $request, Journey $journey): Response | HttpException
    {
        $edited = DB::transaction(function () use ($request, $journey): bool {
            $edited = $this->journeyService->editJourney(
                journey: $journey,
                updatedJourneyData: $request->validated(),
            );

            $this->journeyService->createTrainLocation(
                journey: $journey,
                locationData: [
                    'station_id' => $journey->origin->id,
                    'latitude' => $request->validated(key: "current_location_latitude"),
                    'longitude' => $request->validated(key: "current_location_longitude"),
                ],
            );

            $operator = EmployeeService::currentDeskOperator(journey: $journey);

            if ( ! $operator) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'Please inform your system administration that you have no operator set for the desk you are trying to request a trip.',
                );
            }

            $operator->notify(new JourneyNotification(
                journey: $journey,
                type: NotificationTypes::JOURNEY_EDITED,
            ));

            return $edited;
        });

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Journey creation failed.',
            );
        }

        return response(
            content: [
                'message' => 'Journey updated successfully.',
            ],
            status: Http::ACCEPTED(),
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
    public function confirmLicense(Journey $journey, License $license): Response | HttpException
    {
        $operator = EmployeeService::currentDeskOperator(journey: $journey);

        if ( ! $operator) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Please inform your system administration that you have no operator set for the desk you are trying to request a trip.',
            );
        }

        if ( ! $license->update([
            'status' => LicenseStatuses::CONFIRMED,
        ])) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'License confirmation failed. Please try again',
            );
        }

        $operator->notify(new JourneyNotification(
            journey: $journey,
            type: NotificationTypes::LICENSE_CONFIRMED,
        ));

        return response(
            content: [
                'message' => 'License confirmed successfully. You can begin or continue with your journey.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
