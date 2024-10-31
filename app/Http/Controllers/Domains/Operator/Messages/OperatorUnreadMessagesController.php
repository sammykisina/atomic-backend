<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Messages;

use Domains\Shared\Models\Message;
use Domains\Shared\Resources\MessageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class OperatorUnreadMessagesController
{
    public function __invoke(Request $request): Response
    {
        $messages = QueryBuilder::for(subject: Message::class)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->with(['receiver', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response(
            content: [
                'message' => 'Messages fetched successfully.',
                'messages' => MessageResource::collection(resource: $messages),
            ],
            status: Http::OK(),
        );
    }
}
