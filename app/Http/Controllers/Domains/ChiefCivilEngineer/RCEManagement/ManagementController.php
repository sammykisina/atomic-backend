<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\ChiefCivilEngineer\RCEManagement;

use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Requests\RegionUserRequest;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ManagementController
{
    /**
     * ASSIGNMENT OF RCE TO REGIONS
     * @param RegionUserRequest $request
     * @return HttpException|Response
     */
    public function assign(RegionUserRequest $request)
    {
        $prev_rce_assignment = UserRegion::query()
            ->where('line_id', $request->validated('line_id'))
            ->where('region_id', $request->validated('region_id'))
            ->where('is_active', true)
            ->where('type', 'RCE')
            ->first();

        if ($prev_rce_assignment) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'This region is already assigned to a RCE.',
            );
        }

        $start_station = Station::query()
            ->where('id', $request->start_station_id)
            ->where('line_id', $request->validated('line_id'))
            ->with('section')
            ->first();

        $end_station = Station::query()
            ->where('id', $request->end_station_id)
            ->where('line_id', $request->validated('line_id'))
            ->with('section')
            ->first();

        $user_region = UserRegion::query()->create([
            'user_id' => $request->validated('user_id'),
            'region_id' => $request->validated('region_id'),
            'line_id' => $request->validated('line_id'),
            'start_station_id' => $request->validated('start_station_id'),
            'end_station_id' => $request->validated('end_station_id'),
            'type' => $request->validated('type'),
            'start_kilometer' => $start_station->start_kilometer,
            'end_kilometer' => $end_station->section ? $end_station->section->end_kilometer : $end_station->end_kilometer,
        ]);

        if ( ! $user_region) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Region assigned failed.',
            );
        }

        return Response(
            content: [
                'message' => 'Region assigned successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
