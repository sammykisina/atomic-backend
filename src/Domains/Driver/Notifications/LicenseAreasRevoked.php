<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LicenseAreasRevoked extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public string $description,
        public int $area_id,
        public string $type,
        public NotificationTypes $notificationTypes,
        public int $license_id,
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
            'message' => $this->message,
            'description' => $this->description,
            'type' => $this->notificationTypes->value,
            'area_id' => $this->area_id,
            'area_type' => $this->type,
            'license_id' => $this->license_id,
        ];
    }
}
