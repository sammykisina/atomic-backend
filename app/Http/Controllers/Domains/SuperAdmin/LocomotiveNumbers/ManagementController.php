<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\LocomotiveNumbers;

use Domains\SuperAdmin\Models\LocomotiveNumber;
use Domains\SuperAdmin\Requests\CreateOrEditLocomotiveNumberRequest;
use Domains\SuperAdmin\Resources\LocomotiveNumberResource;
use Domains\SuperAdmin\Services\LocomotiveNumberService;
use Domains\SuperAdmin\Services\ObcService;
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
        $obc = ObcService::getObcById( obc_id: $request->validated(key: 'obc_id'));
        if ($obc->locomotiveNumber) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This obc is already attached to another locomotive number ' . $obc->locomotiveNumber->number. '.',
            );
        }

        $locomotiveNumber = $this->locomotiveNumberService->createLocomotiveNumber(
          attributes: $request->validated(),
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
        $obc = ObcService::getObcById( obc_id: $request->validated(key: 'obc_id'));
        if ($obc->locomotiveNumber && $obc->locomotiveNumber->id !== $locomotiveNumber->id) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This obc is already attached to another locomotive number ' . $obc->locomotiveNumber->number. '. Detach the currently linked loco before you attach it to this locomotive.',
            );
        }

        $edited = $this->locomotiveNumberService->editLocomotiveNumber(
            number: $request->validated(key : 'number'),
            driver_id: $request->validated('driver_id'),
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
