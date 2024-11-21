<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Shifts;

use Domains\Driver\Models\Panic;
use Domains\Driver\Resources\PanicResource;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\SuperAdmin\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class ShiftPanicsController
{
    public function __invoke(Request $request): Response
    {
        $panics = [];
        $shift  = Shift::query()
            ->where('user_id', Auth::id())
            ->where('status', ShiftStatuses::CONFIRMED->value)
            ->where('active', true)
            ->first();

        if ($shift) {
            $panics = QueryBuilder::for(subject: Panic::class)
                ->where('shift_id', $shift->id)
                ->with(relations: [
                    'journey.train.trainName',
                    'journey.train.line',
                    'journey.train.origin',
                    'journey.train.destination',
                    'journey.train.locomotiveNumber',
                    'journey.train.driver',
                    'journey.licenses',
                    'shift',
                ])
                ->allowedFilters(filters: [
                    AllowedFilter::exact(name: 'is_acknowledge'),
                ])
                ->get();
        }



        return response(
            content: [
                'message' => 'Panics fetched successfully.',
                'panics' => PanicResource::collection(
                    resource: $panics,
                ),
            ],
            status: Http::OK(),
        );
    }
}
