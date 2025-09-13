<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Lines;

use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Requests\CreateOrEditLineRequest;
use Domains\SuperAdmin\Resources\LineResource;
use Domains\SuperAdmin\Services\LineService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        private LineService $lineService,
    ) {}

    /**
     * CREATE LINE
     * @param CreateOrEditLineRequest $request
     * @return HttpException|Response
     */
    public function create(CreateOrEditLineRequest $request): HttpException | Response
    {

        $line = DB::transaction(function () use ($request): Line {
            $line = $this->lineService->createLine(
                name: $request->validated(key : 'name'),
            );

            $this->lineService->createLineCounties(
                counties: $request->validated(key : 'counties'),
                line: $line,
            );

            return $line;
        });

        if ( ! $line) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Line creation failed.',
            );
        }

        return response(
            content: [
                'line' => new LineResource(
                    resource: $line,
                ),
                'message' => 'Line created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT LINE
     * @param CreateOrEditLineRequest $request
     * @param Line $line
     * @return HttpException|Response
     */
    public function edit(CreateOrEditLineRequest $request, Line $line): HttpException | Response
    {
        $edited = DB::transaction(function () use ($request, $line): bool {
            $edited = $this->lineService->editLine(
                name: $request->validated(key : 'name'),
                line: $line,
            );

            $this->lineService->createLineCounties(
                counties: $request->validated(key : 'counties'),
                line: $line,
            );

            return $edited;
        });

        if ( ! $edited) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Line update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Line updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }

    /**
     * SHOW LINE
     * @param Line $line
     * @return Response
     */
    public function show(Line $line): Response | HttpException
    {
        $line = $line->where('id', $line->id)
            ->with(relations: ['regions', 'counties', 'stations.section', 'stations.loops', 'stations.triplines'])->first();

        return response(
            content: [
                'message' => 'Line fetched successfully.',
                'line' => new LineResource(
                    resource: $line,
                ),
            ],
            status: Http::OK(),
        );
    }


    /**
     * DELETE LINE
     * @param Line $line
     * @return Response|HttpException
     */
    public function delete(Line $line): Response | HttpException
    {
        if ($line->stations()->exists()) {
            abort(
                code: Http::FORBIDDEN(),
                message: 'Cannot delete line as it has associated stations.Please delete them first.',
            );
        }

        if ( ! $line->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Line deletion failed.',
            );
        }

        return response(
            content: [
                'message' => 'Line deleted successfully.',
            ],
            status: Http::OK(),
        );
    }
}
