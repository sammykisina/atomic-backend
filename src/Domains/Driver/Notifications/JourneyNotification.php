<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class JourneyNotification extends Notification
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
            'origin' => $this->journey->origin,
            'destination' => $this->journey->destination,
            'driver' => $this->journey->driver,
            'journey_id' => $this->journey->id,
            'train_info' => [
                'train_number' => $this->journey->train,
                'service_order' => $this->journey->service_order,
                'number_of_coaches' => $this->journey->number_of_coaches,
            ],
            'type' => NotificationTypes::JOURNEY,
        ];
    }
}
