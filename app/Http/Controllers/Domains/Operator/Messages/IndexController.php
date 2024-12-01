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

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $driver_id = $request->input('driver_id');

        if ( ! $driver_id) {
            return response(
                content: ['message' => 'Driver is required.'],
                status: Http::BAD_REQUEST(),
            );
        }

        $auth_user_id = Auth::id();

        $messages = QueryBuilder::for(subject: Message::class)
            ->where(function ($query) use ($auth_user_id, $driver_id): void {
                $query->where('sender_id', $auth_user_id)
                    ->where('receiver_id', $driver_id);
            })
            ->orWhere(function ($query) use ($auth_user_id, $driver_id): void {
                $query->where('sender_id', $driver_id)
                    ->where('receiver_id', $auth_user_id);
            })
            ->with(['receiver', 'sender','locomotive'])
            // ->orderBy('created_at', 'desc')
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
