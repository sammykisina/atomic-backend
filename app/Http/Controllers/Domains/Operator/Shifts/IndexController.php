<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Shifts;

use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Resources\ShiftResource;
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
            ->allowedIncludes('user', 'desk')
            ->first();

        return response(
            content: [
                'message' => 'Shift fetched successfully.',
                'shift' => $shift ? new ShiftResource(
                    resource: $shift,
                ) : null,
            ],
            status: Http::OK(),
        );
    }
}
