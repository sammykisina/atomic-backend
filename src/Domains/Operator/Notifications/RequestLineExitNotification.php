<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class RequestLineExitNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Journey $journey,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Exit Line Request',
            'description' => 'A line exit request was made by ' . $this->journey->train->driver->fullname . ' with train ' . $this->journey->train->name,
            'journey_id' => $this->journey->id,
            'type' => NotificationTypes::REQUEST_LINE_EXIT->value,
        ];
    }
}
