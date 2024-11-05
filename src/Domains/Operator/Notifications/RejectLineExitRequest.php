<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class RejectLineExitRequest extends Notification
{
    use Queueable;

    public function __construct(
        public Journey $journey,
    ) {}

    /**   @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**  @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Line exit request rejected.',
            'description' => 'Your line exit request for train ' . $this->journey->train->name . ' was rejected.Please continue with your current license allocations.You may also send a direct message to the operator incase you have any questions.',
            'type' => NotificationTypes::LINE_EXIT_REQUEST_REJECTED->value,
        ];
    }
}
