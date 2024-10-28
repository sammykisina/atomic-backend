<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\Messages;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Models\Message;
use Domains\Shared\Requests\MessageRequest;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JustSteveKing\StatusCode\Http;

final class StoreController
{
    public function __invoke(MessageRequest $request, Journey $journey): Response
    {

        $message_data = [];
        $message_data['sender_id'] = Auth::user()->id;
        $message_data['message'] = $request->message;

        if(Auth::user()->type === UserTypes::OPERATOR_CONTROLLER) {
            $message_data['receiver_id'] = $journey->train->driver_id;
        }

        if(Auth::user()->type === UserTypes::DRIVER) {
            $shifts = $journey->shifts;
            $shift = ShiftService::getShiftById(
                shift_id: end($shifts)
            );

            $message_data['receiver_id'] = $shift->user_id;
        }


        $message = Message::query()->create(
            attributes: $message_data
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
