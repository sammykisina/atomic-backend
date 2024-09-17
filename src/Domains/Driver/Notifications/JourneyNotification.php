<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Driver\Resources\LocationResource;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class JourneyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Journey $journey,
        public NotificationTypes $type,
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
            'origin' => new StationResource(
                resource: $this->journey->origin,
            ),
            'destination' => new StationResource(
                resource: $this->journey->destination,
            ),
            'driver' => new UserResource(
                resource: $this->journey->driver,
            ),
            'current_location' => new LocationResource(
                resource: $this->journey->activeLocation,
            ),
            'journey_id' => $this->journey->id,
            'train_info' => [
                'train_number' => $this->journey->train,
                'service_order' => $this->journey->service_order,
                'number_of_coaches' => $this->journey->number_of_coaches,
            ],
            'type' => $this->type,
        ];
    }
}
