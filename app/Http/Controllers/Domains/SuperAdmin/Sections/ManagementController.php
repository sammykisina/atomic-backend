<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SuperAdmin\Sections;

use Domains\SuperAdmin\Exports\SectionsExport;
use Domains\SuperAdmin\Imports\SectionsImport;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Requests\CreateOrEditSectionRequest;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    /**
     * SHOW SECTION
     * @param Section $section
     * @return Response
     */
    public function show(Section $section): Response | HttpException
    {
        return response(
            content: [
                'message' => 'Section fetched successfully.',
                'section' => new SectionResource(
                    resource: $section,
                ),
            ],
            status: Http::OK(),
        );
    }

    /**
     * DELETE SECTION
     * @param Section $section
     * @return Response|HttpException
     */
    public function delete(Section $section): Response | HttpException
    {
        if ( ! $section->delete()) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Section deletion failed.',
            );
        }

        return response(
            content: [
                'message' => 'Section deleted successfully.',
            ],
            status: Http::ACCEPTED(),
        );
    }


    /**
     * EXPORT SECTIONS TEMPLATE
     * @param Excel $excel
     * @return BinaryFileResponse
     */
    public function exportSections(Request $request, Excel $excel): BinaryFileResponse
    {
        return $excel->download(export: new SectionsExport(
            line_id: (int) $request->get('line_id'),
        ), fileName: 'sections.xlsx');
    }

    /**
     * IMPORT SECTIONS
     * @param Request $request
     * @return Response
     */
    public function importSections(Request $request): Response
    {
        $import = new SectionsImport(
            sectionService: $this->sectionService,
            line_id: (int) $request->get('line_id'),
        );

        $import->import($request->file('sections'));

        if ($import->errors()->isNotEmpty()) {
            return response(
                content: [
                    'message' => 'Please check the following issues in your spread sheet',
                    'errors' => $import->errors(),
                ],
                status: Http::NOT_IMPLEMENTED(),
            );
        }

        return response(
            content: [
                'message' => 'Sections uploaded successfully',
            ],
            status: Http::CREATED(),
        );
    }
}
