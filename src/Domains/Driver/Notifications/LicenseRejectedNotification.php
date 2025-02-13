<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Resources\TrainResource;
use Domains\SuperAdmin\Services\TrainService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LicenseRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Journey $journey,
        public string $reason_for_rejection,
    ) {}

    /**  @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**  @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $train = TrainService::getTrainById(
            train_id: $this->journey->train_id,
        );

        return [
            'type' => NotificationTypes::LICENSE_REJECTED->value,
            'message' => "License Rejected",
            'description' => "The License you assigned to train " . $this->journey->train->locomotiveNumber->number . " has been rejected with a reason [ " . $this->reason_for_rejection . " ]",
            'train' => new TrainResource(
                resource: $train,
            ),
        ];
    }
}
