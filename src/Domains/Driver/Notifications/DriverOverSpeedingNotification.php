<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class DriverOverSpeedingNotification extends Notification
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
            'type' => NotificationTypes::DRIVER_OVER_SPEEDING->value,
            'message' => 'Over Speeding',
            'description' => 'Your train is currently over-speeding. Please adjust your speed to the displayed limit.',
        ];
    }
}
