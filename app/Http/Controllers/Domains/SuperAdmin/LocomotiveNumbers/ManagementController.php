<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\LocomotiveNumbers;

use Domains\SuperAdmin\Models\LocomotiveNumber;
use Domains\SuperAdmin\Requests\CreateOrEditLocomotiveNumberRequest;
use Domains\SuperAdmin\Resources\LocomotiveNumberResource;
use Domains\SuperAdmin\Services\LocomotiveNumberService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected LocomotiveNumberService $locomotiveNumberService,
    ) {}

    /**
     * CREATE LOCOMOTIVE NUMBER
     * @param CreateOrEditLocomotiveNumberRequest $request
     * @return HttpException|Response
     */
    public function create(CreateOrEditLocomotiveNumberRequest $request): HttpException | Response
    {

        $locomotiveNumber = $this->locomotiveNumberService->createLocomotiveNumber(
            number: $request->validated(key : 'number'),
        );

        if ( ! $locomotiveNumber) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Locomotive Number creation failed.',
            );
        }

        return response(
            content: [
                'locomotiveNumber' => new LocomotiveNumberResource(
                    resource: $locomotiveNumber,
                ),
                'message' => 'Locomotive number created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT LOCOMOTIVE NUMBER
     * @param CreateOrEditLocomotiveNumberRequest $request
     * @param LocomotiveNumber $locomotiveNumber
     * @return HttpException|Response
     */
    public function edit(CreateOrEditLocomotiveNumberRequest $request, LocomotiveNumber $locomotiveNumber): HttpException | Response
    {
        $edited = $this->locomotiveNumberService->editLocomotiveNumber(
            number: $request->validated(key : 'number'),
            locomotiveNumber: $locomotiveNumber,
        );

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Locomotive number update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Locomotive number updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW LOCOMOTIVE NUMBER
     * @param LocomotiveNumber $locomotiveNumber
     * @return Response
     */
    public function show(LocomotiveNumber $locomotiveNumber): Response | HttpException
    {
        return response(
            content: [
                'message' => 'Locomotive number fetched successfully.',
                'locomotive_number' => new LocomotiveNumberResource(
                    resource: $locomotiveNumber,
                ),
            ],
            status: Http::OK(),
        );
    }
}
