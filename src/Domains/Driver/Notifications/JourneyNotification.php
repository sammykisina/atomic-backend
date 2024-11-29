<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Resources\TrainResource;
use Domains\SuperAdmin\Services\TrainService;
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
        $train = TrainService::getTrainById(
            train_id: $this->journey->train_id,
        );

        return [
            'journey' => [
                'journey_id' => $this->journey->id,
                'requesting_location' => $this->journey->requesting_location,
                'last_destination' => $this->journey->last_destination,
                'train' => new TrainResource(
                    resource: $train,
                ),
            ],
            'type' => $this->type,
        ];
    }
}
