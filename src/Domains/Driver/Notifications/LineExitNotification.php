<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LineExitNotification extends Notification
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
            'message' => 'Line exit request accepted.',
            'description' => 'Your line exit request for train ' . $this->journey->train->name . ' was accepted.',
            'type' => NotificationTypes::LINE_EXIT_REQUEST_ACCEPTED->value,
        ];
    }
}
