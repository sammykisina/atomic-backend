<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class DriverUnauthorizedAreaNotification extends Notification
{
    use Queueable;

    public function __construct(

    ) {}

    /**
     * GET THE NOTIFICATION'S DELIVERY CHANNELS
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * GET THE ARRAY REPRESENTATION OF THE NOTIFICATION
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationTypes::DRIVER_IN_UNAUTHORIZED_AREA->value,
            'message' => 'Unauthorized Area',
            'description' => 'Your train is currently in unauthorized area. Please contact your operator now.',
        ];
    }
}
