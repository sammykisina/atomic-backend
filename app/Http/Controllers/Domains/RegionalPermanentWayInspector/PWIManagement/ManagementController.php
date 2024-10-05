<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalPermanentWayInspector\PWIManagement;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\RegionalPermanentWayInspector\Requests\PWIKilometerAssignmentRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * ASSIGNMENT OF PWI TO REGIONS
     * @param PWIKilometerAssignmentRequest $request
     * @return HttpException|Response
     */
    public function assign(PWIKilometerAssignmentRequest $request): HttpException | Response
    {
        $user = Auth::user();
        $prev_pwi_assignment = UserRegion::query()
            ->where('line_id', $request->validated('line_id'))
            ->where('region_id', $user->region_id)
            ->where('start_kilometer', $request->validated('start_kilometer'))
            ->where('end_kilometer', $request->validated('end_kilometer'))
            ->where('is_active', true)
            ->where('type', 'PWI')
            ->first();

        if ($prev_pwi_assignment) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The selected kilometers are already assigned to a PWI',
            );
        }

        $user = Auth::user();
        $assignment = UserRegion::query()->create([
            'user_id' => $request->validated('user_id'),
            'region_id' => $user->region_id,
            'line_id' => $request->validated('line_id'),
            'start_kilometer' => $request->validated('start_kilometer'),
            'end_kilometer' => $request->validated('end_kilometer'),
            'type' => 'PWI',
            'owner_id' => $user->id,
        ]);

        if ( ! $assignment) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Kilometer assignment failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Kilometers assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
