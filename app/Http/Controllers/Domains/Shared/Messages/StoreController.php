<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Messages;

use Carbon\Carbon;
use Domains\Operator\Enums\ShiftStatuses;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\Message;
use Domains\Shared\Requests\MessageRequest;
use Domains\SuperAdmin\Models\LocomotiveNumber;
use Domains\SuperAdmin\Models\Shift;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class StoreController
{
    public function __invoke(MessageRequest $request): Response
    {

        $locomotive = LocomotiveNumber::query()
            ->where('driver_id', Auth::id())
            ->first();

        $message_data = [];
        $message_data['sender_id'] = Auth::id();
        $message_data['message'] = $request->message;
        $message_data['locomotive_id'] = $locomotive->id;


        if (UserTypes::DRIVER === Auth::user()->type) {
            $today = Carbon::today()->format('Y-m-d');
            $currentTime = Carbon::now()->format('H:i:s');

            $shift = Shift::whereDate('day', $today)
                ->whereTime('from', '<=', $currentTime)
                ->whereTime('to', '>=', $currentTime)
                ->where('active', true)
                ->where('status', ShiftStatuses::CONFIRMED->value)
                ->first();

            if ( ! $shift) {
                abort(
                    code: Http::EXPECTATION_FAILED(),
                    message: 'We could not find a receiver. Please check if there is an active shift',
                );
            }

            $message_data['receiver_id'] = $shift->user_id;
        }


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
