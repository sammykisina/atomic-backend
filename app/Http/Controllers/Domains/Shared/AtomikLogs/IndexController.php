<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\AtomikLogs;

use Domains\Shared\Models\AtomikLog;
use Domains\Shared\Resources\AtomikLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $atomic_logs = QueryBuilder::for(subject: AtomikLog::class)
            ->with(relations: [
                'resourceble',
                'actor',
                'receiver',
                'train',
            ])
            ->orderBy('created_at', 'desc')
            ->get();


        return response(
            content: [
                'message' => 'Atomik logs fetched successfully.',
                'atomik_logs' => AtomikLogResource::collection(
                    resource: $atomic_logs,
                ),
            ],
            status: Http::OK(),
        );
    }
}
