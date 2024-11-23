<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\SeniorEngineer\Speeds;

use Domains\PrincipleEngineer\Enums\RegionAssignmentTypes;
use Domains\PrincipleEngineer\Models\UserRegion;
use Domains\SeniorEngineer\Models\Speed;
use Domains\SeniorEngineer\Resources\SpeedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $current_se_assignment = QueryBuilder::for(subject: UserRegion::class)
            ->where('user_id', Auth::id())
            ->where('type', RegionAssignmentTypes::SE)
            ->first();

        $speeds = QueryBuilder::for(subject: Speed::class)
            ->with(relations: ['line', 'areable'])
            ->whereBetween('start_kilometer', [$current_se_assignment->start_kilometer, $current_se_assignment->end_kilometer])
            ->whereBetween('end_kilometer', [$current_se_assignment->start_kilometer, $current_se_assignment->end_kilometer])
            ->get();

        return response(
            content: [
                'message' => 'speeds fetched successfully.',
                'speeds' => SpeedResource::collection(
                    resource: $speeds,
                ),
            ],
            status: Http::OK(),
        );
    }
}
