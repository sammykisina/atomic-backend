<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\License;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LicenseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public License $license,
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
            'license' => [
                'id' => $this->license->id,
                'journey_id' => $this->license->journey_id,
                'license_number' => $this->license->license_number,
                'from' => new StationResource(
                    resource: $this->license->originStation,
                ),
                'to' => new StationResource(
                    resource: $this->license->destinationStation,
                ),
                'to_stop_in' => $this->license->main_id ? 'main line' : new LoopResource(
                    $this->license->loop,
                ),
            ],
            'type' => $this->type,
        ];
    }
}
