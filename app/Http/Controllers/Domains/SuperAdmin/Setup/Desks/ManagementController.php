<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Setup\Desks;

use Domains\SuperAdmin\Models\Desk;
use Domains\SuperAdmin\Requests\Setup\CreateOrEditDeskRequest;
use Domains\SuperAdmin\Resources\DeskResource;
use Domains\SuperAdmin\Services\DeskService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected DeskService $deskService,
    ) {}

    /**
     * CREATE DESK
     * @param CreateOrEditDeskRequest $request
     * @return HttpException|Response
     */
    public function create(CreateOrEditDeskRequest $request): HttpException | Response
    {

        $desk = $this->deskService->createDesk(
            deskData: $request->validated(),
        );

        if ( ! $desk) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Desk creation failed.',
            );
        }

        return response(
            content: [
                'desk' => new DeskResource(
                    resource: $desk,
                ),
                'message' => 'Desk created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT DESK
     * @param CreateOrEditDeskRequest $request
     * @param Desk $desk
     * @return HttpException|Response
     */
    public function edit(CreateOrEditDeskRequest $request, Desk $desk): HttpException | Response
    {

        if ( ! $this->deskService->editDesk(
            updatedDeskData: $request->validated(),
            desk: $desk,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Desk update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Desk updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW DESK
     * @param Desk $desk
     * @return Response
     */
    public function show(Desk $desk): Response | HttpException
    {
        return response(
            content: [
                'message' => 'Desk fetched successfully.',
                'desk' => new DeskResource(
                    resource: $desk,
                ),
            ],
            status: Http::OK(),
        );
    }
}
