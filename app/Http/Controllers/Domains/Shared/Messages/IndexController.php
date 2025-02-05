<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Messages;

use Domains\Shared\Models\Message;
use Domains\Shared\Resources\MessageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $messages = QueryBuilder::for(Message::class)
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->with([
                'receiver',
                'sender',
                'locomotive',
            ])
            ->get();


        return response(
            content: [
                'message' => 'Message fetched successfully.',
                'messages' => MessageResource::collection(
                    resource: $messages,
                ),
            ],
            status: Http::OK(),
        );
    }
}
