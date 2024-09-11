<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Sections;

use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Requests\CreateOrEditSectionRequest;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Services\SectionService;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    public function __construct(
        protected SectionService $sectionService,
    ) {}

    /**
     * CREATE SECTION
     * @param CreateOrEditSectionRequest $request
     * @return Response | HttpException
     */
    public function create(CreateOrEditSectionRequest $request): HttpException | Response
    {
        $section = $this->sectionService->createSection(
            sectionData: $request->validated(),
        );

        if ( ! $section) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Section creation failed.',
            );
        }

        return response(
            content: [
                'section' => new SectionResource(
                    resource: $section,
                ),
                'message' => 'Section created successfully.',
            ],
            status: Http::CREATED(),
        );
    }

    /**
     * EDIT SECTION
     * @param CreateOrEditSectionRequest $request
     * @param Section $section
     * @return Response | HttpException
     */
    public function edit(CreateOrEditSectionRequest $request, Section $section): Response | HttpException
    {
        if ( ! $this->sectionService->editSection(
            section: $section,
            updatedSectionData: $request->validated(),
        )) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Section update failed.',
            );
        }

        return response(
            content: [
                'message' => 'Section updated successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }
}
