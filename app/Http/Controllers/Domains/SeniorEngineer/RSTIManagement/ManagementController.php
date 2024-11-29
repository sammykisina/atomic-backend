<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer\RSTIManagement;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\SeniorEngineer\Requests\RSTIKilometerAssignmentRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * ASSIGNMENT OF RCE TO REGIONS
     * @param RSTIKilometerAssignmentRequest $request
     * @return HttpException|Response
     */
    public function assign(RSTIKilometerAssignmentRequest $request)
    {
        $prev_rsti_assignment = UserRegion::query()
            ->where('line_id', $request->validated('line_id'))
            ->where('region_id', $request->validated('region_id'))
            ->where('start_kilometer', $request->validated('start_kilometer'))
            ->where('end_kilometer', $request->validated('end_kilometer'))
            ->where('is_active', operator: true)
            ->where('type', RegionAssignmentTypes::RSTI->value)
            ->first();

        if ($prev_rsti_assignment) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The selected kilometers are already assigned to a RSTI',
            );
        }

        $user_region = UserRegion::query()->create([
            'user_id' => $request->validated('user_id'),
            'region_id' => $request->validated('region_id'),
            'line_id' => $request->validated('line_id'),
            'start_kilometer' => $request->validated('start_kilometer'),
            'end_kilometer' => $request->validated('end_kilometer'),
            'type' => $request->validated('type'),
            'owner_id' => Auth::id(),
        ]);

        if ( ! $user_region) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Section blocks assignment failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Section block assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
