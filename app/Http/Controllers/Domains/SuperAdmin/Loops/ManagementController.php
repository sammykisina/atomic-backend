<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Loops;

use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Requests\CreateOrEditLoopRequest;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Services\LoopService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected LoopService $loopService,
    ) {}

    /**
     * CREATE LOOP
     * @param CreateOrEditLoopRequest $request
     * @return Response | HttpException
     */
    public function create(CreateOrEditLoopRequest $request, Line $line, Station $station): HttpException | Response
    {
        $loop = $this->loopService->createLoop(
            loopData: $request->validated(),
            station: $station,
            line: $line,
        );

        if ( ! $loop) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Loop creation failed.',
            );
        }

        return response(
            content: [
                'loop' => new LoopResource(
                    resource: $loop,
                ),
                'message' => 'Loop created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT LOOP
     * @param CreateOrEditLoopRequest $request
     * @param Loop $loop
     * @return Response | HttpException
     */
    public function edit(CreateOrEditLoopRequest $request, Station $station, Loop $loop): Response | HttpException
    {
        if( ! $this->loopService->editLoop(
            loop: $loop,
            updatedLoopData: $request->validated(),
            station: $station,
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Loop update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Loop updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
