<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class DriverCurrentLocationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $operator,
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
            'operator' => $this->operator,
            'type' => NotificationTypes::CURRENT_DRIVER_LOCATION,
        ];
    }
}
