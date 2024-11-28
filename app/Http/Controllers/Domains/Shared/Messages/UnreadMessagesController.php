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

final class UnreadMessagesController
{
    public function __invoke(Request $request): Response
    {
        $unread_messages = QueryBuilder::for(subject: Message::class)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->with(['receiver', 'sender', 'locomotive.driver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response(
            content: [
                'message' => 'Messages fetched successfully.',
                'unread_messages' => MessageResource::collection(resource: $unread_messages),
            ],
            status: Http::OK(),
        );
    }
}
