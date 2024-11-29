<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\License;
use Domains\Shared\Enums\NotificationTypes;
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
                'direction' => $this->license->direction,
                'origin' => $this->license->origin,
                'destination' => $this->license->destination,
            ],
            'type' => $this->type,
        ];

    }
}
