<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Operator\Messages;

use Domains\Operator\Requests\StoreMessageRequest;
use Domains\Shared\Models\Message;
use Domains\SuperAdmin\Models\LocomotiveNumber;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class StoreController
{
    public function __invoke(StoreMessageRequest $request): Response
    {
        $locomotive = LocomotiveNumber::query()
            ->where('id', $request->validated('locomotive_id'))
            ->first();

        $message_data = [];
        $message_data['sender_id'] = Auth::user()->id;
        $message_data['message'] = $request->validated(key: 'message');
        $message_data['receiver_id'] = $locomotive->driver_id;
        $message_data['locomotive_id'] = $locomotive->id;


        $message = Message::query()->create(
            attributes: $message_data,
        );

        if ( ! $message) {
            abort(
                code: Http::EXPECTATION_FAILED(),
                message: 'Message not send.Please try again',
            );
        }

        return response(
            content: [
                'message' => 'Message created successfully.',
            ],
            status: Http::CREATED(),
        );
    }
}
