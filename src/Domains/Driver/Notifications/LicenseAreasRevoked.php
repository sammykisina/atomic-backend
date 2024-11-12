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
            'message' => 'The license areas have been revoked',
            'description' => 'Please be advised that some sections in your active license have been revoked (canceled). You can view your new license adjustments on your screen.',
            'type' => NotificationTypes::LICENSE_AREAS_REVOKED->value,
        ];
    }
}
