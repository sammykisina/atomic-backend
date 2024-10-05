<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Shifts;

use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Models\Station;
use Domains\SuperAdmin\Resources\ShiftResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $shift  = QueryBuilder::for(subject: Shift::class)
            ->where('user_id', Auth::id())
            ->allowedIncludes('user', 'startStation', 'endStation')
            ->first();

        $stations = Station::where('line_id', $shift->line_id)
            ->whereBetween('id', [
                $shift->startStation->id,
                $shift->endStation->id,
            ])
            ->with('section', 'loops')
            ->orderBy('id', 'asc')
            ->get();

        return response(
            content: [
                'message' => 'Shift fetched successfully.',
                'shift' => new ShiftResource(
                    resource: $shift ?? null,
                ),
                'stations' => StationResource::collection(
                    resource: $stations,
                ),
            ],
            status: Http::OK(),
        );
    }
}
