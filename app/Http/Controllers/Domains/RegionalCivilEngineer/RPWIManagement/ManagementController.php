<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalCivilEngineer\RPWIManagement;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\PermanentWayInspector\Requests\RPWIKilometerAssignmentRequest;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * ASSIGNMENT OF RCE TO REGIONS
     * @param RPWIKilometerAssignmentRequest $request
     * @return HttpException|Response
     */
    public function assign(RPWIKilometerAssignmentRequest $request): HttpException | Response
    {
        $prev_rpwi_assignment = UserRegion::query()
            ->where('line_id', $request->validated('line_id'))
            ->where('region_id', $request->validated('region_id'))
            ->where('start_kilometer', $request->validated('start_kilometer'))
            ->where('end_kilometer', $request->validated('end_kilometer'))
            ->where('is_active', true)
            ->first();

        if ($prev_rpwi_assignment) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The selected kilometers are already assigned to a RPWI',
            );
        }

        $user_region = UserRegion::query()->create([
            'user_id' => $request->validated('user_id'),
            'region_id' => $request->validated('region_id'),
            'line_id' => $request->validated('line_id'),
            'start_kilometer' => $request->validated('start_kilometer'),
            'end_kilometer' => $request->validated('end_kilometer'),
            'type' => $request->validated('type'),
        ]);

        if ( ! $user_region) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Section blocks assigned failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Section blocks successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
