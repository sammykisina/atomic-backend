<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Driver\Journey;

use Domains\Driver\Models\Journey;
use Domains\Driver\Requests\CreateOrEditJourneyRequest;
use Domains\Driver\Requests\LocationRequest;
use Domains\Driver\Resources\JourneyResource;
use Domains\Driver\Services\JourneyService;
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
        if ( ! $this->journeyService->editJourney(
            journey: $journey,
            updatedJourneyData: $request->validated(),
        )) {
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
            updatedJourneyData: $request->validated(),
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
}
