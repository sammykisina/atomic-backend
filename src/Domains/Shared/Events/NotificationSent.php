<?php

declare(strict_types=1);

namespace Domains\Shared\Events;

use Domains\Shared\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class NotificationSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public User $receiver,
        public User $sender,
        public string $message,
    ) {}

    public function broadcastWith(): array
    {
        return [
            "sender" => $this->sender,
            "receiver" => $this->receiver,
            "message" => $this->message,
        ];
    }

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        Log::info('receiver ' . $this->receiver->id);

        return [
            new PrivateChannel("notification." . $this->sender->id),
        ];
    }
}
