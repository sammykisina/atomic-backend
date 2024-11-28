<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class OperatorUnauthorizedAreaNotification extends Notification
{
    use Queueable;

    /**
     * CREATE A NEW NOTIFICATION INSTANCE.
     */
    public function __construct(
        public Journey $journey,
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
            'message' => 'Driver in unauthorized area',
            'description' => 'Be advised that driver with train ' . $this->journey->train->trainName->name . ' is currently in an unauthorized area.',
        ];
    }
}
