<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class OperatorExitLineRequestNotification extends Notification
{
    use Queueable;

    /**
     * CREATE A NEW NOTIFICATION INSTANCE
     */
    public function __construct(
        public Journey $journey,
    ) {}

    /**
     * GET THE NOTIFICATION?S DELIVERY CHANNELS.
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * GET THE ARRAY REPRESENTATION OF THE NOTIFICATION.
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Exit Line Request',
            'description' => 'Are your sure you want to exit train ' . $this->journey->train->trainName->name . ' with driver ' . $this->journey->train->driver->fullname . ' ?',
            'journey_id' => $this->journey->id,
            'type' => NotificationTypes::REQUEST_LINE_EXIT->value,
        ];
    }
}
