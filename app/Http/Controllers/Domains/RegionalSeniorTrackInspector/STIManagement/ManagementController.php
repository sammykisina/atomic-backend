<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\RegionalSeniorTrackInspector\STIManagement;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\RegionalSeniorTrackInspector\Requests\STIKilometerAssignmentRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * ASSIGNMENT OF STI TO REGIONS
     * @param STIKilometerAssignmentRequest $request
     * @return HttpException|Response
     */
    public function assign(STIKilometerAssignmentRequest $request): HttpException | Response
    {
        $user = Auth::user();
        $prev_sti_assignment = UserRegion::query()
            ->where('line_id', $request->validated('line_id'))
            ->where('region_id', $user->region_id)
            ->where('start_kilometer', $request->validated('start_kilometer'))
            ->where('end_kilometer', $request->validated('end_kilometer'))
            ->where('is_active', true)
            ->where('type', RegionAssignmentTypes::STI->value)
            ->first();

        if ($prev_sti_assignment) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The selected kilometers are already assigned to STI',
            );
        }

        $user_region = UserRegion::query()
            ->where('user_id', $request->validated('user_id'))
            ->where('line_id', $request->validated('line_id'))
            ->where('is_active', true)
            ->first();

        if ($user_region) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'The selected STI is already assigned to another section block within this line',
            );
        }

        $user = Auth::user();
        $assignment = UserRegion::query()->create([
            'user_id' => $request->validated('user_id'),
            'region_id' => $user->region_id,
            'line_id' => $request->validated('line_id'),
            'start_kilometer' => $request->validated('start_kilometer'),
            'end_kilometer' => $request->validated('end_kilometer'),
            'type' => RegionAssignmentTypes::STI->value,
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
