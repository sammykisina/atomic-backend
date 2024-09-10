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
        protected LineService $lineService,
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

            $this->lineService->createLineRegions(
                regions: $request->validated(key: 'regions'),
                line: $line,
            );

            $this->lineService->createLineCounties(
                counties: $request->validated(key :'counties'),
                line:$line,
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

    public function edit(CreateOrEditLineRequest $request, Line $line): HttpException | Response
    {
        $edited = DB::transaction(function () use ($request, $line): bool {
            $edited = $this->lineService->editLine(
                name: $request->validated(key : 'name'),
                line: $line,
            );

            $this->lineService->createLineRegions(
                regions: $request->validated(key: 'regions'),
                line: $line,
            );

            $this->lineService->createLineCounties(
                counties: $request->validated(key :'counties'),
                line:$line,
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
}
