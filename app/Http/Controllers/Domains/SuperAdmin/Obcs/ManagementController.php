<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Obcs;

use Domains\SuperAdmin\Models\Obc;
use Domains\SuperAdmin\Requests\CreateOrEditObcRequest;
use Domains\SuperAdmin\Resources\ObcResource;
use Domains\SuperAdmin\Services\ObcService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;

final class ManagementController
{
    /** @param ObcService $obcService */
    public function __construct(
        protected ObcService $obcService,
    ) {}


    /**
     * CREATE OBC
     * @param CreateOrEditObcRequest $request
     * @return Response
     */
    public function create(CreateOrEditObcRequest $request): Response
    {
        $obc = $this->obcService->createObc(
            obcData: $request->validated(),
        );

        if ( ! $obc) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Obc creation failed.Please try again.',
            );
        }

        return response(
            content: [
                'obc' => new ObcResource(
                    resource: $obc,
                ),
                'message' => 'Obc created successfully.',
            ],
            status: Http::CREATED(),
        );
    }


    /**
     * EDIT OBC
     * @param CreateOrEditObcRequest $request
     * @param Obc $obc
     * @return Response
     */
    public function edit(CreateOrEditObcRequest $request, Obc $obc): Response
    {
        $edited = $this->obcService->editObc(
            updatedObcData: $request->validated(),
            obc: $obc,
        );

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Obc update failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Obc updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW OBC
     * @param Obc $obc
     * @return Response
     */
    public function show(Obc $obc): Response
    {
        $obc = ObcService::getObcById(
            obc_id: $obc->id,
        );

        return response(
            content: [
                'message' => 'Obc fetched successfully.',
                'obc' => new ObcResource(
                    resource: $obc,
                ),
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * DELETE OBC
     * @param Obc $obc
     * @return Response
     */
    public function delete(Obc $obc): Response
    {
        $deleted = $obc->delete();

        if ( ! $deleted) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Obc deletion failed.Please try again.',
            );
        }

        return response(
            content: [
                'message' => 'Obc deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
