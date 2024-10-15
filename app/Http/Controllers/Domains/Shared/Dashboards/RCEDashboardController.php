<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Dashboards;

use Carbon\Carbon;
use Domains\ChiefCivilEngineer\Models\UserRegion;
use Domains\ChiefCivilEngineer\Resources\UserRegionResource;
use Domains\Inspector\Models\Inspection;
use Domains\Inspector\Resources\InspectionResource;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class RCEDashboardController
{
    public function __invoke(Request $request): Response
    {
        $inspections = [];

        $your_rpwis = UserRegion::query()
            ->where('type', 'RPWI')
            ->where('owner_id', Auth::id())
            ->with(['line', 'region', 'user'])
            ->get();

        if ($request->query('date')) {
            $date = Carbon::parse($request->query('date'));
            $inspections = Inspection::query()
                ->whereDate('created_at', $date)
                ->whereHas('inspectionSchedule.inspector', function ($query): void {
                    $query->where('region_id', Auth::user()->region_id);
                })
                ->with([
                    'inspectionSchedule.owner.userRegion.owner.userRegion',
                    'inspectionSchedule.inspector',
                    'inspectionSchedule.line',
                    'issues.issueName',
                    'issues.issueArea.station',
                    'issues.issueArea.section',
                    'issues.assignment',
                    'issues.inspection.inspectionSchedule.inspector',
                ])
                ->get();
        } else {
            $startDate = Carbon::now()->subDays(30);
            $inspections = Inspection::query()
                ->whereBetween('created_at', [$startDate, Carbon::now()])
                ->whereHas('inspectionSchedule', function ($query): void {
                    $query->where('region_id', Auth::user()->region_id);
                })
                ->with([
                    'inspectionSchedule.owner.userRegion.owner.userRegion',
                    'inspectionSchedule.inspector',
                    'inspectionSchedule.line',
                    'issues.issueName',
                    'issues.issueArea.station',
                    'issues.issueArea.section',
                    'issues.inspection.inspectionSchedule.inspector',
                    'issues.assignment',
                ])
                ->get();
        }

        $number_of_gang_persons = User::query()
            ->where('type', UserTypes::GANG_MAN)
            ->where('region_id', Auth::user()->region_id)
            ->count();

        $number_of_inspectors = User::query()
            ->where('type', UserTypes::INSPECTOR)
            ->where('region_id', Auth::user()->region_id)
            ->count();

        return response(
            content: [
                'message' => 'RCE dashboard fetched successfully.',
                'rce_dashboard' => [
                    'your_rpwis' => UserRegionResource::collection(
                        resource: $your_rpwis,
                    ),
                    'inspections' => InspectionResource::collection(
                        resource: $inspections,
                    ),
                    'number_of_gang_persons' => $number_of_gang_persons,
                    'number_of_inspectors' => $number_of_inspectors,
                ],

            ],
            status: Http::OK(),
        );
    }
}
