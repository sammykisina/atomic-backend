<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Messages;

use Domains\Shared\Models\Message;
use Domains\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;
use Spatie\QueryBuilder\QueryBuilder;

final class OperatorMakeAsReadController
{
    public function __invoke(Request $request, User $driver): Response
    {
        $messages = QueryBuilder::for(subject: Message::class)
            ->where('receiver_id', Auth::id())
            ->where('sender_id', $driver->id)
            ->whereNull('read_at')
            ->with(['receiver', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($messages as $message) {
            if ( ! $message->update(['read_at' => now()])) {
                abort(
                    code: Http::EXPECTATION_FAILED,
                    message: 'Message not marked as read. Please try again',
                );
            }
        }

        return response(
            content: [
                'message' => 'Message marked as read successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
