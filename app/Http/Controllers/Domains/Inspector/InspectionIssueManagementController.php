<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Inspector;

use Domains\Inspector\Requests\IssueRequest;
use Domains\Inspector\Services\InspectionService;
use Domains\ReginalCivilEngineer\Models\Inspection;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class InspectionIssueManagementController
{
    public function __construct(
        protected InspectionService $inspectionService,
    ) {}
    public function create(IssueRequest $request, Inspection $inspection): HttpException | Response
    {
        $issue = $this->inspectionService->createInspectionIssue(
            inspection: $inspection,
            inspectionIssueData: [
                'condition' => $request->validated('condition'),
                'description' => $request->validated('description') ?? null,
                'latitude' => $request->validated('latitude'),
                'longitude' => $request->validated('longitude'),
                'image_url' => $request->validated('image_url') ?? null,
            ],
        );

        if ( ! $issue) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Issue not added reported successfully. Please retry adding the issue. Incase the problem persists, you can navigate to your list inspections and add this issue manually.',
            );
        }

        return Response(
            content: [
                'message' => 'Issue reported successfully. You can continue with your inspection.',
            ],
            status: Http::CREATED(),
        );
    }
}
